<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Meeting;

use App\Http\Requests\StoreMeetingValid;

class MeetingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['index','show']]);
    }

    /**
     * @SWG\Get(
     *     path="/api/v1/meeting",
     *     summary="Get list of All Meetings",
     *     tags={"Meeting"},
     *     @SWG\Response(
     *         response=200,
     *         description="successful operation",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref="#/definitions/Meeting")
     *         ),
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Not found",
     *     ),
     * )
     */

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $meetings = Meeting::all();
        foreach ($meetings as $meeting){
            $meeting->view_meeting = [
                'href' => 'api/v1/meeting/'.$meeting->id,
                'method' => 'GET'
            ];
        }
        $response = [
            'msg' => 'List of all Meetings',
            'meetings' => $meetings
        ];
        return response()->json($response, 200);
    }

    /**
     * @SWG\Post(
     *     path="/api/v1/meeting",
     *     summary="Add a new meeting to the store",
     *     tags={"Meeting"},
     *     description="Create meeting",
     *     security={{"JWT":{}}},
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         description="Create meeting",
     *         required=true,
     *         @SWG\Schema(
     *              @SWG\Property(
     *                  property="title",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="description",
     *                  type="string",
     *                  format="text"
     *              ),
     *              @SWG\Property(
     *                  property="time",
     *                  type="string",
     *                  format="date-time"
     *              ),
     *              @SWG\Property(
     *                  property="user_id",
     *                  type="integer"
     *              )
     *         ),
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="successful operation",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref="#/definitions/Meeting")
     *         ),
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized user",
     *     ),
     * )
     */

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreMeetingValid $request)
    {

        /*$this->validate($request, [
            'title' => 'required',
            'description' => 'required',
            'time' => 'required',
            'user_id' => 'required'
        ]);*/

        //$validated = $request->validated();

        $title = $request->input('title');
        $description = $request->input('description');
        $time = $request->input('time');
        $user_id = $request->input('user_id');
        //------------------
        //$input = $request->all();
        //firstOrCreator();




        /* без модели и БД
        $meeting = [
            'title' => $title,
            'description' => $description,
            'time' => $time,
            'user_id' => $user_id,
            'view_meeting' => [
                'href' => 'api/v1/meeting/1',
                'method' => 'GET'
            ]
        ];

        $response = [
            'msg' => 'Meeting Created',
            'data' => $meeting
        ];

        return $response()->json($response, 201);
        */

        //через модель
        $meeting = new Meeting([
            'time' => $time,
            'title' => $title,
            'description' => $description
        ]);
        if($meeting->save()){
            $meeting->users()->attach($user_id);
            $meeting->view_meeting = [
                'href' => 'api/v1/meeting/'.$meeting->id,
                'method' => 'GET'
            ];
            $message = [
                'msg' => 'Meeting created',
                'meeting' => $meeting
            ];
            return response()->json($message, 201);
        }

        $response = [
            'msg' => 'Error during creation'
        ];
        return response()->json($response, 404);
    }

    /**
     * @SWG\Get(
     *     path="/api/v1/meeting/{meeting_id}",
     *     summary="Get meeting by id",
     *     tags={"Meeting"},
     *     description="Get meeting by id",
     *     @SWG\Parameter(
     *         name="meeting_id",
     *         in="path",
     *         description="Meeting id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="successful operation",
     *         @SWG\Schema(ref="#/definitions/Meeting"),
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized user",
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Meeting is not found",
     *     )
     * )
     */

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $meeting = Meeting::with('users')->where('id',$id)->firstOrFail();
        $meeting->view_meetings = [
            'href' => 'api/v1/meeting',
            'method' => 'GET'
        ];
        $response = [
            'msg' => 'Meeting information',
            'meeting' => $meeting
        ];
        return response()->json($response, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreMeetingValid $request, $id)
    {
        /*$this->validate($request, [
            'title' => 'required',
            'description' => 'required',
            'time' => 'required|date_format:YmdHie',
            'user_id' => 'required'
        ]);*/

        $title = $request->input('title');
        $description = $request->input('description');
        $time = $request->input('time');
        $user_id = $request->input('user_id');

        $meeting = Meeting::with('users')->findOrFail($id);

        if(!$meeting->users()->where('user_id', $user_id)->first()){
            return response()->json([
                'msg' => 'user not registered for meeting, update not successful'
            ], 401);
        }

        $meeting->time = $time;
        $meeting->title = $title;
        $meeting->description = $description;

        if(!$meeting->update()){
            return response()->json([
                'msg' => 'Error during update'
            ], 404);
        }

        $meeting->view_meeting = [
            'href' => 'api/v1/meeting/' . $meeting->id,
            'method' => 'GET'
        ];

        $response = [
            'msg' => 'Meeting update',
            'meeting' => $meeting
        ];

        return response()->json($response, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $meeting = Meeting::findOrFail($id);
        $users = $meeting->users;
        $meeting->users()->detach();

        if(!$meeting->delete()){
            foreach ($users as $user){
                $meeting->users()->attach($user);
            }
            return response()->json([
                'msg' => 'Delete Failed'
            ], 404);
        }
        $response = [
            'msg' => 'Meeting deleted',
            'create' => [
                'href' => 'api/v1/meeting',
                'method' => 'POST',
                'params' => 'title, description, time'
            ]
        ];

        return response()->json($response, 200);
    }
}
