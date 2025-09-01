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








    public function welcome(){

        $slides = Slides::whereNotIn('status', ['pending', 'rejected'])->get();



        return view('welcome', ['slides' => $slides]);
    }


    public function filter(Request $request){
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $query = Activity_logs::query();

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
        $Activity_logs = $query->get();

        return view('admin.activity', compact('Activity_logs'));
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

        $slides = Slides::all();
        $slideCount = Slides::count();
        $userCount = User::count();


        $slidesPending = Slides::where('status', 'pending')->get()->count();


        $slidesPublish = Slides::where('status', 'published')->get()->count();

        return view('admin.home', ['slides' => $slides, 'slideCount' => $slideCount, 'userCount' => $userCount, 'slidesPending' => $slidesPending, 'slidesPublish' => $slidesPublish ]);

    }


    public function users(){
        if(Auth::user()->role === 'master admin'){
            $users = User::all();

            // where('role', 'user')->get();

            $this->data['users'] = $users;
            return view('admin.users', $this->data);
        } else {
            return redirect()->route('admin.dashboard');
        }
    }


    function activity(){

        $Activity_logs = Activity_logs::all();

        $this->data['Activity_logs'] = $Activity_logs;
        return view('admin.activity', $this->data);





    }


    public function addSlide(){
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

        // dd($request);
        $video = $request->file('file_name');
        $name_database = $video->getClientOriginalName();
        $data['file'] = $name_database;
        $data['added_by_email'] = $request->added_by_email;
        $data['department'] = $request->department;


        $name = $video->getClientOriginalName();
        $path = public_path('image_upload');
        $video->move($path,$name);

        $slide_insert = Slides::create($data);

        if(!$slide_insert){
            return redirect(route('admin.dashboard'))->with('error', 'Slide added failed');
        }else{

            $user_data['name'] = $request->user_add_name;
            $user_data['email'] = $request->user_add_email;
            $user_data['activity'] = $request->user_add_activity;

            Activity_logs::create($user_data);

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

            // Add activity log
            $user_data['name'] = $request->user_add_name;
            $user_data['email'] = $request->user_add_email;
            $user_data['activity'] = $request->user_add_activity;

            Activity_logs::create($user_data);

            DB::commit();

            // Redirect the user or return a response as needed
            return redirect(route('admin.dashboard'))->with('success', 'Slide updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            // Handle the exception, log it, or return an error response
            return redirect()->back()->with('error', 'An error occurred while updating the slide.');
        }
    }



    public function updateVideo(Request $request, Slides $slide)
    {
        // Validate the request
        $request->validate([
            'new_file_name' => 'required|file|mimes:mp4,ogg|max:50000', // Adjust max file size as needed
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

            // Add activity log
            $user_data['name'] = $request->user_add_name;
            $user_data['email'] = $request->user_add_email;
            $user_data['activity'] = $request->user_add_activity;

            Activity_logs::create($user_data);

            DB::commit();

            // Redirect or return a response
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

        //Add activity log
        $user_data['name'] = $request->user_add_name;
        $user_data['email'] = $request->user_add_email;
        $user_data['activity'] = $request->user_add_activity;
        Activity_logs::create($user_data);
        return redirect(route('admin.dashboard'))->with('success', 'Slide updated successfully');


    }


    public function rejectFile(Request $request, Slides $slide)
    {
        // Update the status column to the desired value
        $slide->update(['status' => 'rejected']);

        //Add activity log
        $user_data['name'] = $request->user_add_name;
        $user_data['email'] = $request->user_add_email;
        $user_data['activity'] = $request->user_add_activity;

        Activity_logs::create($user_data);






        return redirect(route('admin.dashboard'))->with('success', 'Slide updated successfully');


    }
















    public function destroy(Slides $slide, Request $request){
        DB::transaction(function () use ($slide, $request) {
            // Delete the slide
            $slide->delete();

            // Add activity log
            $user_data['name'] = $request->user_add_name;
            $user_data['email'] = $request->user_add_email;
            $user_data['activity'] = $request->user_add_activity;

            Activity_logs::create($user_data);
        });

        return redirect(route('admin.dashboard'))->with('success', 'Slide Deleted successfully');
    }






    //user controller
    function addUser(){
        return view('admin.addUser', $this->data);
    }


    function editUser(User $user){
        $this->data['user'] = $user;

        return view('admin.editUser',$this->data);
    }



    function addUserPost(Request $request){

        // dd($request);

        $request->validate([
            'department' => "required",
            "first_name" => "required",
            "last_name" => "required",
            'email' => 'required|email|unique:users', // Add the table name 'users'
            'username' => 'required',
            "password" => "required"
        ]);



        $data['first_name'] = $request->first_name;
        $data['last_name'] = $request->last_name;
        $data['middle_name'] = $request->middle_name;
        $data['email'] = $request->email;
        $data['username'] = $request->username;
        $data['password'] = Hash::make($request->password);
        $data['department'] = $request->department;

        $user = User::create($data);



        if(!$user){
            return redirect(route('admin.users'))->with('error', 'Register Details are not valid');
        }

        return redirect(route('admin.users'))->with('success', 'User added successfully');



        // dd($request);
    }



    public function updateUser(Request $request, User $user)
    {

        // dd($request);

        // // Validate the incoming request data
        $validatedData = $request->validate([
            'department' => 'required',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'username' => 'required|',
            'password' => 'nullable|string', // Password is now nullable
            'status' => 'nullable|string',
        ]);

        // If password is provided, hash it; otherwise, remove it from the validated data
        if ($request->filled('password')) {
            $validatedData['password'] = Hash::make($request->password);
        } else {
            unset($validatedData['password']);
        }

        // Update the user model with the validated data
        $user->update($validatedData);

        // Optionally, you can redirect the user back to a specific page
        return redirect()->route('admin.users')->with('success', 'User updated successfully');
    }


    function destroyUser(User $user){
        // dd($user);

        if($user->role !== 'user'){

            return redirect(route('admin.users'))->with('error', 'This is admin');
        }else{
             $user->delete();
              return redirect(route('admin.users'))->with('success', 'User Deleted successfully');
        }

    }



    function pendingSlides(){



        if(Auth::user()->role !== 'user'){
            $slides = Slides::where('status', '=', 'pending')->get();

            return view('admin.pending', ['slides' => $slides] + $this->data);
        } else {
            return redirect()->route('admin.dashboard');
        }


    }







}
