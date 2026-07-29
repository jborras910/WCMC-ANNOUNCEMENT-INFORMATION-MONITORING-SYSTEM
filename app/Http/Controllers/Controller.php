<?php

namespace App\Http\Controllers;
use App\User;
use App\Slides;
use App\Activity_logs;
use Illuminate\Auth\Events\Authenticated;
use Illuminate\Support\Facades\Auth; // Add this line
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Department;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $data;

    public function __construct()
    {
        // Apply the 'auth' middleware to the dashboard and users methods
        // $this->middleware('auth')->only(['dashboard', 'users', 'activity']);
        $slidesPending = Slides::where('status', 'pending')->get()->count();

        $this->data['slidesPending'] = $slidesPending;
    }




    public function layouts(){
        // Event::listen(Authenticated::class, function($event){
        //     $slidesPending = Slides::where('status', 'pending')->get()->count();

        //      $this->data['slidesPending'] = $slidesPending;
        //     return $this->data;
        // });

    }








    // Editing/deleting a specific slide by id is gated by the "manage-slides"
    // permission at the route level, but that alone doesn't stop a marketing
    // user from touching a business-department slide just by guessing its id.
    // This closes that gap: only "view-all-departments" holders (master_admin)
    // may reach across department lines.
    protected function authorizeSlideAccess(Slides $slide): void
    {
        if (Auth::user()->cannot('view-all-departments') && $slide->department_id !== Auth::user()->department_id) {
            abort(403);
        }
    }

    // $category groups log entries for the filter dropdown on /activity (e.g.
    // "user", "role", "permission", "department", "slide"). $subjectEmail is
    // the *affected* account when it differs from the actor — e.g. who a
    // role was granted to — so "what happened to this person's access" is a
    // filter, not a text search.
    protected function logActivity(string $action, ?string $category = null, ?string $subjectEmail = null): void
    {
        Activity_logs::create([
            'name'          => Auth::user()->first_name . ' ' . Auth::user()->last_name,
            'email'         => Auth::user()->email,
            'activity'      => Auth::user()->first_name . ' ' . Auth::user()->last_name . ' ' . $action,
            'category'      => $category,
            'subject_email' => $subjectEmail,
        ]);
    }

    // Renders "added X, Y; removed Z" between two lists of role/permission
    // names — used so audit entries say exactly what changed, not just that
    // something did. Returns null when there's no difference to report.
    protected function describeSetChanges(string $label, array $before, array $after): ?string
    {
        $added = array_values(array_diff($after, $before));
        $removed = array_values(array_diff($before, $after));

        if (!$added && !$removed) {
            return null;
        }

        $parts = [];
        if ($added) {
            $parts[] = 'added ' . implode(', ', $added);
        }
        if ($removed) {
            $parts[] = 'removed ' . implode(', ', $removed);
        }

        return $label . ': ' . implode('; ', $parts);
    }
    public function welcome(){

        $slides = $this->publicSlidesQuery()->get();

        return view('welcome', ['slides' => $slides, 'withSound' => true]);
    }

    public function welcomeQueue(){

        $slides = $this->publicSlidesQuery()->get();

        return view('welcome-queue', ['slides' => $slides, 'withSound' => true]);
    }

    // Shared by welcome(), welcomeQueue(), and currentSlides() — the combined
    // lobby feed and its live-polling endpoint. When this server is
    // dedicated to one department (DEFAULT_DISPLAY_DEPARTMENT in .env), all
    // three only ever show that department's slides.
    protected function publicSlidesQuery(){
        $query = Slides::whereNotIn('status', ['pending', 'rejected'])->orderBy('order');

        $defaultDepartmentSlug = config('display.default_department');
        if ($defaultDepartmentSlug) {
            $query->whereHas('department', function ($departmentQuery) use ($defaultDepartmentSlug) {
                $departmentQuery->where('slug', $defaultDepartmentSlug);
            });
        }

        return $query;
    }

    // A department's own dedicated display screen (e.g. Marketing's lobby TV)
    // — same player as the combined welcome() screen, just scoped to slides
    // that belong to this department only.
    public function departmentWelcome(Department $department){
        $slides = Slides::whereNotIn('status', ['pending', 'rejected'])
            ->where('department_id', $department->id)
            ->orderBy('order')
            ->get();

        return view('welcome', [
            'slides' => $slides,
            'pollUrl' => route('display.current', ['department' => $department]),
            'withSound' => true,
        ]);
    }

    public function departmentCurrentSlides(Department $department){
        $videos = Slides::whereNotIn('status', ['pending', 'rejected'])
            ->where('department_id', $department->id)
            ->orderBy('order')
            ->get()
            ->map(function ($slide) {
                return [
                    'id' => $slide->id,
                    'url' => asset('image_upload/' . $slide->file),
                ];
            });

        return response()->json(['videos' => $videos]);
    }

    // Polled from the lobby TV pages (welcome / welcome-queue) so a newly
    // added, edited, or (re)approved slide shows up automatically — without
    // this, the display only ever sees the slide list it was handed on the
    // last page load/reload.
    public function currentSlides(){
        $videos = $this->publicSlidesQuery()
            ->get()
            ->map(function ($slide) {
                return [
                    'id' => $slide->id,
                    'url' => asset('image_upload/' . $slide->file),
                ];
            });

        return response()->json(['videos' => $videos]);
    }

    // Persists the drag-and-drop order set on the admin Slides table. The
    // same "order" column drives playback order on the lobby TVs, so
    // rearranging here changes the sequence videos loop in.
    public function reorderSlides(Request $request){
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:slides_table,id',
        ]);

        // Non-privileged users only ever see their own department's slides in
        // the dashboard table, so this scopes the update the same way — a
        // stray/forged id for another department is just silently a no-op.
        $canViewAll = Auth::user()->can('view-all-departments');

        foreach ($request->order as $position => $slideId) {
            $query = Slides::where('id', $slideId);
            if (!$canViewAll) {
                $query->where('department_id', Auth::user()->department_id);
            }
            $query->update(['order' => $position]);
        }

        return response()->json(['success' => true]);
    }


    public function filter(Request $request){
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $category = $request->category;

        $query = Activity_logs::query();

        if (Gate::denies('view-all-activity-logs')) {
            $query->where('email', Auth::user()->email);
        }

        if ($category) {
            $query->where('category', $category);
        }

        if ($start_date && $end_date) {
            // Both start and end dates are provided, so apply the date filters
            $query->whereDate('created_at', '>=', $start_date)
                  ->whereDate('created_at', '<=', $end_date);
        } elseif ($start_date) {
            // Only start date is provided, filter by start date
            $query->whereDate('created_at', '>=', $start_date);
        } elseif ($end_date) {
            // Only end date is provided, filter by end date
            $query->whereDate('created_at', '<=', $end_date);
        }

        // Execute the query and retrieve the results
        $Activity_logs = $query->orderBy('created_at', 'desc')->get();
        $categories = Activity_logs::whereNotNull('category')->distinct()->orderBy('category')->pluck('category');

        return view('admin.activity', compact('Activity_logs', 'categories') + $this->data);
    }




    public function dataTable(){
        return view('dataTable');
    }


    public function login(){
        if (Auth::check()) {
            return redirect(route('admin.dashboard'));
        }

        return view('login');
    }

    public function register(){
        if (Auth::check()) {
            return redirect(route('admin.dashboard'));
        }

        return view('register');
    }


    public function loginPost(Request $request){

        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ], [
            'username.required' => 'Please enter your username.',
            'password.required' => 'Please enter your password.'
        ]);

        $credentials = $request->only('username','password');

        $user = User::where('username', $request->username)->first();

        if($user && $user->status !== 'Active'){
            return redirect()->back()->with('status', 'Your account is not active. Please contact support.');
        }

        if(Auth::attempt($credentials)){
            return redirect()->intended(route('admin.dashboard'));
        }

        // If authentication failed, return back with error message and input data
        return redirect()->back()->with('status', 'Login Details are not valid')->withInput($request->except('_token'));
    }





    public function registrationPost(Request $request){

        $request->validate([
            "first_name" => "required",
            "last_name" => "required",
            "middle_name" => "required",
            'email' => 'required|email|unique:users', // Add the table name 'users'
            "password" => "required"
        ]);



        $data['first_name'] = $request->first_name;
        $data['last_name'] = $request->last_name;
        $data['middle_name'] = $request->middle_name;
        $data['email'] = $request->email;
        $data['password'] = Hash::make($request->password);

        $user = User::create($data);



        if(!$user){
            return redirect(route('register'))->with('status', 'Register Details are not valid');
        }

        return redirect(route('login'))->with('status', 'Register successfully');
        // dd($request);
    }


    public function logout(){
        // Clear the session and log the user out
        Session::flush();
        Auth::logout();
        return redirect(route('login'));
    }



    public function dashboard(){

        $slidesQuery = Slides::with('department')->orderBy('order');
        if (Auth::user()->cannot('view-all-departments')) {
            $slidesQuery->where('department_id', Auth::user()->department_id);
        }
        $slides = $slidesQuery->get();

        $slideCount = Slides::count();
        $userCount = User::count();


        $slidesPending = Slides::where('status', 'pending')->get()->count();


        $slidesPublish = Slides::where('status', 'published')->get()->count();

        return view('admin.home', ['slides' => $slides, 'slideCount' => $slideCount, 'userCount' => $userCount, 'slidesPending' => $slidesPending, 'slidesPublish' => $slidesPublish ]);

    }


    public function users(){
        $users = User::with('department')->get();
        $this->data['users'] = $users;
        return view('admin.users', $this->data);
    }


    function activity(){

        $query = Activity_logs::query();

        if (Gate::denies('view-all-activity-logs')) {
            $query->where('email', Auth::user()->email);
        }

        $this->data['Activity_logs'] = $query->orderBy('created_at', 'desc')->get();
        $this->data['categories'] = Activity_logs::whereNotNull('category')->distinct()->orderBy('category')->pluck('category');

        return view('admin.activity', $this->data);
    }


    public function addSlide(){
        // Anyone who can see every department (master_admin) picks which
        // department a slide belongs to; everyone else is locked to their own.
        if (Auth::user()->can('view-all-departments')) {
            $this->data['departments'] = Department::orderBy('name')->get();
        }

        return view('admin.addSlide', $this->data);
    }


    // public function addSlidePost(Request $request){
    //     $image = $request->file('file_name');

    //     $name_database = $image->getClientOriginalName();

    //     $request->validate([
    //         "title" => "required",
    //         "description" => "required",

    //     ]);

    //     $data['title'] = $request->title;
    //     $data['description'] = $request->description;
    //     $data['file'] = $name_database;

    //     $name = $image->getClientOriginalName();
    //     $path = public_path('image_upload');
    //     $image->move($path,$name);

    //     $slide_insert = Slides::create($data);


    //     if(!$slide_insert){
    //         return redirect(route('admin.dashboard'))->with('error', 'Slide added failed');
    //     }else{
    //         return redirect(route('admin.dashboard'))->with('success', 'Slide added successfully');
    //     }

    // }






    public function addVideoslide(Request $request){

        $canPickDepartment = Auth::user()->can('view-all-departments');

        $request->validate([
            'file_name' => 'required|file|mimes:mp4|max:102400', // MP4 only, 100MB max
            'department_id' => $canPickDepartment ? 'required|exists:departments,id' : 'nullable',
        ], [
            'file_name.mimes' => 'Only MP4 video files are allowed. Please convert your video to MP4 before uploading.',
            'department_id.required' => 'Please select which department this slide belongs to.',
        ]);

        $video = $request->file('file_name');
        $name_database = $video->getClientOriginalName();
        $data['file'] = $name_database;
        $data['added_by_email'] = $request->added_by_email;
        $data['department_id'] = $canPickDepartment ? $request->department_id : Auth::user()->department_id;
        $data['order'] = (int) Slides::max('order') + 1; // new slides play last, not first


        $name = $video->getClientOriginalName();
        $path = public_path('image_upload');
        $video->move($path,$name);

        $slide_insert = Slides::create($data);

        if(!$slide_insert){
            return redirect(route('admin.dashboard'))->with('error', 'Slide added failed');
        }else{
            $this->logActivity('Added a slide', 'slide');
            return redirect(route('admin.dashboard'))->with('success', 'Slide added successfully');
        }
    }








    public function addDocumentslide(Request $request){
        $document = $request->file('file_name');
        $name_database = $document->getClientOriginalName();
        $data['file'] = $name_database;

        $name = $document->getClientOriginalName();
        $path = public_path('image_upload');
        $document->move($path,$name);

        $slide_insert = Slides::create($data);
        if(!$slide_insert){
            return redirect(route('admin.dashboard'))->with('error', 'Slide added failed');
        }else{
            return redirect(route('admin.dashboard'))->with('success', 'Slide added successfully');
        }
    }









    function editSlide(Slides $slide){
        $this->authorizeSlideAccess($slide);

        $this->data['slide'] = $slide;
        return view('admin.editSlide', $this->data);
    }


    public function updateSlide(Request $request, Slides $slide)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            // Check if a new image file has been uploaded
            if ($request->hasFile('new_file_name')) {
                // Handle file upload
                $uploadedFile = $request->file('new_file_name');
                $fileName = time() . '_' . $uploadedFile->getClientOriginalName();
                $uploadedFile->move(public_path('image_upload'), $fileName);

                // Update the file name in the database
                $slide->file = $fileName;
            }

            // Update other fields
            $slide->title = $validatedData['title'];
            $slide->description = $validatedData['description'];

            // Save the changes to the database
            $slide->save();

            $this->logActivity('Edited a slide', 'slide');
            DB::commit();

            return redirect(route('admin.dashboard'))->with('success', 'Slide updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred while updating the slide.');
        }
    }



    public function updateVideo(Request $request, Slides $slide)
    {
        $this->authorizeSlideAccess($slide);

        // Validate the request
        $request->validate([
            'new_file_name' => 'required|file|mimes:mp4|max:102400', // MP4 only, 100MB max
        ], [
            'new_file_name.mimes' => 'Only MP4 video files are allowed. Please convert your video to MP4 before uploading.',
        ]);

        try {
            DB::beginTransaction();

            // Check if a new file is uploaded
            if ($request->hasFile('new_file_name')) {


                // $video = $request->file('file_name');
                // $name = $video->getClientOriginalName();
                // $path = public_path('image_upload');
                // $video->move($path,$name);


                $video = $request->file('new_file_name');

                $name = $video->getClientOriginalName();
                $path = public_path('image_upload');
                $video->move($path,$name);
                // Update the slide's file attribute with the new file name
                $slide->update(['file' => $name, 'status' => 'pending']);

            }

            $this->logActivity('Replaced a slide video', 'slide');
            DB::commit();

            return redirect(route('admin.dashboard'))->with('success', 'Slide updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            // Handle the exception, log it, or return an error response
            return redirect()->back()->with('error', 'An error occurred while updating the slide.');
        }
    }

    public function publishFile(Request $request, Slides $slide)
    {
        // Update the status column to the desired value
        $slide->update(['status' => 'published']);

        $this->logActivity('Published a slide', 'slide');
        return redirect(route('admin.dashboard'))->with('success', 'Slide published successfully');
    }


    public function rejectFile(Request $request, Slides $slide)
    {
        // Update the status column to the desired value
        $slide->update(['status' => 'rejected']);

        $this->logActivity('Rejected a slide', 'slide');
        return redirect(route('admin.dashboard'))->with('success', 'Slide rejected successfully');
    }
















    public function destroy(Slides $slide, Request $request){
        $this->authorizeSlideAccess($slide);

        $slide->delete();
        $this->logActivity('Deleted a slide', 'slide');
        return redirect(route('admin.dashboard'))->with('success', 'Slide deleted successfully');
    }






    //user controller
    function addUser(){
        $this->data['roles'] = Role::orderBy('name')->get();
        $this->data['permissions'] = Permission::orderBy('name')->get();
        $this->data['departments'] = Department::orderBy('name')->get();

        return view('admin.addUser', $this->data);
    }


    function editUser(User $user){
        $this->data['user'] = $user;
        $this->data['roles'] = Role::orderBy('name')->get();
        $this->data['permissions'] = Permission::orderBy('name')->get();
        $this->data['departments'] = Department::orderBy('name')->get();

        return view('admin.editUser',$this->data);
    }



    function addUserPost(Request $request){

        $request->validate([
            'department_id' => "required|exists:departments,id",
            "first_name" => "required",
            "last_name" => "required",
            'email' => 'required|email|unique:users', // Add the table name 'users'
            'username' => 'required',
            "password" => "required",
            'roles' => 'array',
            'roles.*' => 'exists:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ]);



        $data['first_name'] = $request->first_name;
        $data['last_name'] = $request->last_name;
        $data['middle_name'] = $request->middle_name;
        $data['email'] = $request->email;
        $data['username'] = $request->username;
        $data['password'] = Hash::make($request->password);
        $data['department_id'] = $request->department_id;

        $user = User::create($data);



        if(!$user){
            return redirect(route('admin.users'))->with('error', 'Register Details are not valid');
        }

        $roles = $request->input('roles', []);
        $permissions = $request->input('permissions', []);
        $user->syncRoles($roles);
        $user->syncPermissions($permissions);

        $details = array_filter([
            $roles ? 'roles: ' . implode(', ', $roles) : null,
            $permissions ? 'extra permissions: ' . implode(', ', $permissions) : null,
        ]);
        $this->logActivity(
            'created user ' . $user->email . ($details ? ' (' . implode('; ', $details) . ')' : ''),
            'user',
            $user->email
        );

        return redirect(route('admin.users'))->with('success', 'User added successfully');
    }



    public function updateUser(Request $request, User $user)
    {

        // // Validate the incoming request data
        $validatedData = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'username' => 'required|',
            'password' => 'nullable|string', // Password is now nullable
            'status' => 'nullable|string',
            'roles' => 'array',
            'roles.*' => 'exists:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        // If password is provided, hash it; otherwise, remove it from the validated data
        if ($request->filled('password')) {
            $validatedData['password'] = Hash::make($request->password);
        } else {
            unset($validatedData['password']);
        }

        $roles = $validatedData['roles'] ?? [];
        $permissions = $validatedData['permissions'] ?? [];
        unset($validatedData['roles'], $validatedData['permissions']);

        // Snapshot access before it changes, so the log says exactly what moved.
        $rolesBefore = $user->getRoleNames()->toArray();
        $permissionsBefore = $user->getDirectPermissions()->pluck('name')->toArray();
        $departmentBefore = optional($user->department)->name;

        // Update the user model with the validated data
        $user->update($validatedData);
        $user->syncRoles($roles);
        $user->syncPermissions($permissions);

        $roleChange = $this->describeSetChanges('roles', $rolesBefore, $roles);
        $permissionChange = $this->describeSetChanges('extra permissions', $permissionsBefore, $permissions);
        $departmentAfter = optional($user->fresh()->department)->name;
        $departmentChange = $departmentBefore !== $departmentAfter
            ? 'department: ' . ($departmentBefore ?? 'none') . ' -> ' . ($departmentAfter ?? 'none')
            : null;

        $changes = array_filter([$roleChange, $permissionChange, $departmentChange]);
        if ($changes) {
            $this->logActivity(
                'updated access for ' . $user->email . ' (' . implode('; ', $changes) . ')',
                'user',
                $user->email
            );
        }

        // Optionally, you can redirect the user back to a specific page
        return redirect()->route('admin.users')->with('success', 'User updated successfully');
    }


    function destroyUser(User $user){
        $email = $user->email;
        $user->delete();

        $this->logActivity('deleted user ' . $email, 'user', $email);

        return redirect(route('admin.users'))->with('success', 'User Deleted successfully');
    }



    function pendingSlides(){
        $slides = Slides::with('department')->where('status', '=', 'pending')->get();
        return view('admin.pending', ['slides' => $slides] + $this->data);
    }







}
