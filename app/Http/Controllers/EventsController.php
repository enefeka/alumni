<?php

namespace App\Http\Controllers;

use App\Events;
use App\Groups;
use App\Comments;
use App\Users;
use App\Roles;
use App\Types;
use App\Privacity;
use App\Belong;
use App\Asign;
use Illuminate\Http\Request;
use \Firebase\JWT\JWT;

class EventsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function post_create()
    {
        $headers = getallheaders();
        $token = $headers['Authorization'];
        $key = $this->key;
        $userData = JWT::decode($token, $key, array('HS256'));
        $id_user = $userData->id;
        $user = Users::find($id_user);
        if ($user->id !== 1) {
            return $this->error(401, 'No tienes permiso');
        }
        $title = $_POST['title'];
        $description = $_POST['description'];
        $array_id_group = $_POST['id_group'];
        $id_type = $_POST['id_type'];

        if (empty($_POST['title']) || empty($_POST['description']) || empty($_POST['id_group']) || empty($_POST['id_type'])) {
            return $this->error(400, 'Todos los datos son obligatorios');
        }
        try {

            $eventDB = new Events();
            $eventDB->title = $title;
            $eventDB->description = $description;

            $typeDB = Types::find($id_type);
            if (empty($typeDB)) {
                return $this->error(400, 'No existe el tipo de evento indicado');
            }

            $eventDB->id_type = $id_type;

            if (!empty($_POST['lat'])) {
                $eventDB->lat = $_POST['lat'];
            }
            if (!empty($_POST['lon'])) {
                $eventDB->lon = $_POST['lon'];
            }
            if (!empty($_POST['url'])) {
                $eventDB->url = $_POST['url'];
            }

            date_default_timezone_set('CET');
            $eventDB->date = date('Y-m-d H:i:s');
            $eventDB->image = 2;




            $eventDB->id_user = $user->id;
            $eventDB->save();
            foreach ($array_id_group as $key => $idGroup) {

                $groupDB = Groups::find($idGroup);
                if (empty($groupDB)) {
                    $eventDB->delete();
                    return $this->error(400, 'No existe el tipo de grupo indicado');
                }
                $asignDB = new Asign();
                $asignDB->id_event = $eventDB->id;
                $asignDB->id_group = $idGroup;
                $asignDB->save();
            }


            return $this->error(200, 'Evento creado');


            
        } catch (Exception $e) {
            return $this->error(500, $e->getMessage());
        }
    }
    
    public function post_update()
    {
        $headers = getallheaders();
        $token = $headers['Authorization'];
        $key = $this->key;
        $userData = JWT::decode($token, $key, array('HS256'));
        $id_user = $userData->id;
        $user = Users::find($id_user);
        if ($user->id !== 1) {
            return $this->error(401, 'No tienes permiso');
        }
        $id = $_POST['id'];

        if (empty($_POST['id'])) {
            return $this->error(400, 'Falta el parámetro id');
        }
        try {
            $eventBD = Events::find($id);
            if ($eventBD == null) {
                return $this->error(400, 'El evento no existe');
            }
            if (!empty($_POST['title']) ) {
                $eventBD->title = $_POST['title'];
            }
            if (!empty($_POST['description']) ) {
                $eventBD->description = $_POST['description'];
            }
            if (!empty($_POST['lat']) ) {
                $eventBD->lat = $_POST['lat'];
            }
            if (!empty($_POST['lon']) ) {
                $eventBD->lon = $_POST['lon'];
            }
            if (!empty($_POST['url']) ) {
                $eventBD->url = $_POST['url'];
            }


            $eventBD->save();
            return $this->error(200, 'Evento actualizado');

            
        } catch (Exception $e) {
           
           return $this->error(500, $e->getMessage());

        }
    }


    public function get_events()
    {
        $headers = getallheaders();
        $token = $headers['Authorization'];
        $key = $this->key;

        if (!isset($_GET['type'])) {
            return $this->error(400, 'El parámetro type es obligatorio (0 -> todos, 1 -> eventos, 2 -> ofertas trabajo, 3 -> notificaciones, 4 -> noticias)');
        }
        $type = $_GET['type'];
        if ($type == 0) {
            $events = Events::whereNotNull('id')->get();
            $eventTitles = [];
            $eventDescriptions = [];
            foreach ($events as $event) {
                array_push($eventTitles, $event->title);
                array_push($eventDescriptions, $event->description);
        }
            return response()->json([
                'eventos' => $eventTitles,
                'descripción' => $eventDescriptions,
        ]);
        }
        if ($type == 1) {
            $events = Events::where('id_type', 1)->get();
            $eventTitles = [];
            $eventDescriptions = [];
            foreach ($events as $event) {
                array_push($eventTitles, $event->title);
                array_push($eventDescriptions, $event->description);
        }
            return response()->json([
                'eventos' => $eventTitles,
                'descripción' => $eventDescriptions,
        ]);
        }
        if ($type == 2) {
            $events = Events::where('id_type', 2)->get();
            $eventTitles = [];
            $eventDescriptions = [];
            foreach ($events as $event) {
                array_push($eventTitles, $event->title);
                array_push($eventDescriptions, $event->description);
        }
            return response()->json([
                'eventos' => $eventTitles,
                'descripción' => $eventDescriptions,
        ]);
        }
        if ($type == 3) {
            $events = Events::where('id_type', 3)->get();
            $eventTitles = [];
            $eventDescriptions = [];
            foreach ($events as $event) {
                array_push($eventTitles, $event->title);
                array_push($eventDescriptions, $event->description);
        }
            return response()->json([
                'eventos' => $eventTitles,
                'descripción' => $eventDescriptions,
        ]);
        }
        if ($type == 4) {
            $events = Events::where('id_type', 4)->get();
            $eventTitles = [];
            $eventDescriptions = [];
            foreach ($events as $event) {
                array_push($eventTitles, $event->title);
                array_push($eventDescriptions, $event->description);
        }
            return response()->json([
                'eventos' => $eventTitles,
                'descripción' => $eventDescriptions,
        ]);
        }
    } 

    public function get_event()
    {
        $headers = getallheaders();
        $token = $headers['Authorization'];
        $key = $this->key;

        if (!isset($_GET['id'])) {
            return $this->error(400, 'El parámetro id es obligatorio');
        }
        $id = $_GET['id'];
        try {
            $event = Events::find($id);
            if ($event == null) {
                return $this->error(400, 'No existe el evento');
            }
            $commentsBD = Comments::where('id_event', $id)->get();


            foreach ($commentsBD as $key => $comment) {
                $userBD = Users::find($comment->id_user);
                $comment['username'] = $userBD->username;
                $comment['id_user'] = $userBD->id;
                $comment['photo'] = $userBD->photo;
            }

            return response()->json([
                'eventos' => $event,
                'comentarios' => $commentsBD,
        ]);
            
        } catch (Exception $e) {
            return $this->error(500, $e->getMessage());
            
        }
        }
    public function post_delete()
    {
        $headers = getallheaders();
        $token = $headers['Authorization'];
        $key = $this->key;
        $userData = JWT::decode($token, $key, array('HS256'));
        $id_user = $userData->id;
        $user = Users::find($id_user);
        if ($user->id !== 1) {
            return $this->error(401, 'No tienes permiso');
        }
        $id = $_POST['id'];
        if (empty($_POST['id'])) {
            return $this->error(400, 'Introduce la id del evento');
        }
        try {
            $eventBD = Events::find($id);
            if ($eventBD == null) {
                return $this->error(400, 'El evento no existe');
            }
            if ($eventBD->id_user == $user->id || $user->id_rol == 1) {
                $eventBD->delete();
                return $this->error(200, 'El evento ha sido borrado');
            }
            return $this->error(401, 'No autorizado');
        
        } catch (Exception $e) {
            return $this->error(500, $e->getMessage());
        }
    }

    
    public function  get_find()
    {
        $headers = getallheaders();
        $token = $headers['Authorization'];
        $key = $this->key;
        $userData = JWT::decode($token, $key, array('HS256'));
        $id = Users::where('email', $userData->email)->first()->id;
        if (empty($_GET['search'])) 
        {
          return $this->error(400, 'Falta parámetro obligatorio (search)');
        }
        $search = $_GET['search'];
        if (!isset($_GET['type']) )
        {
          return $this->error(400, 'Faltan parámetros obligatorios (type, 0 -> todos, 1-> eventos, 2-> ofertas trabajo, 3 -> notificaciones, 4 -> noticias)');
        }

        $type = $_GET['type'];

        if ($type == 0) {
            $query = Events::where('title', 'like', $search)->get();
            return $this->error(200, 'Eventos', $query->title);
        }
   

    }


    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Events  $events
     * @return \Illuminate\Http\Response
     */
    public function show(Events $events)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Events  $events
     * @return \Illuminate\Http\Response
     */
    public function edit(Events $events)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Events  $events
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Events $events)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Events  $events
     * @return \Illuminate\Http\Response
     */
    public function destroy(Events $events)
    {
        //
    }
}