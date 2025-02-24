<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SabanasController extends Controller
{
    public function index()
    {
        $listasabanas = [
            [
                'url' => "getsabanas19",
                'titulo' => '19. Revisión del 15% de las habitaciones del hotel'
            ],
            [
                'url' => "getsabanas21",
                'titulo' => '21. Realizacion del mantenimiento del 15% de las habitaciones del hotel aplicacion del formato técnico'
            ],
            [
                'url' => "getlimpieza",
                'titulo' => 'Limpieza'
            ]
        ];
        return view('sabanas.sabanas', compact('listasabanas'));
    }

    public function getsabanas21()
    {
        $idApp = 16;
        $punto = 21;
        $titulo_pregunta = "Numero de habitacion";
        $correo = \Auth::user()->email;

        $query = "SELECT id_sucursal FROM tb_sucursal WHERE id_sucursal=(SELECT id_sucursal FROM tb_usuario WHERE correo='$correo' LIMIT 1) LIMIT 1";
        $idSucursal = DB::select($query);
        foreach ($idSucursal as $ids) {
            $idsuc = $ids->id_sucursal;
        }
        $query = "SELECT * FROM tb_habitaciones WHERE id_sucursal='$idsuc'";
        $habitaciones = DB::select($query);
        $query = "SELECT id_encuesta FROM tb_encuesta_bloque WHERE id_encuesta IN (SELECT id_encuesta FROM tb_encuesta WHERE id_app='$idApp') AND numero='$punto' LIMIT 1";
        $encuesta = DB::select($query);
        foreach ($encuesta as $enc) {
            $idEncuesta = $enc->id_encuesta;
        }
        $query = "SELECT id_bloque FROM tb_encuesta_bloque WHERE id_encuesta='$idEncuesta' AND numero='$punto' LIMIT 1";
        $bloque = DB::select($query);
        foreach ($bloque as $bloq) {
            $idBloque = $bloq->id_bloque;
        }
        $query = "SELECT id_pregunta FROM tb_encuesta_pregunta WHERE c_titulo_pregunta LIKE '%$titulo_pregunta%' AND c_tipo_pregunta='Numerico' AND id_encuesta='$idEncuesta' LIMIT 1";
        $pregunta = DB::select($query);
        foreach ($pregunta as $preg) {
            $idPregunta = $preg->id_pregunta;
        }
        $query = "SELECT
            clave_registro,
            respuesta,
            MAX(SUBSTR(fecha,1,10)) as fecha
            FROM tb_respuesta
            WHERE sucursal='$idsuc'
            AND idcuestionario='$idEncuesta'
            AND idbloque='$idBloque'
            AND idpregunta='$idPregunta'
            AND respuesta IS NOT NULL
            AND respuesta <> ''
            GROUP BY respuesta
            ORDER BY fecha DESC";

        $respuestas = DB::select($query);

        if (!$habitaciones) {
            return '<h3>No hay habitaciones registradas</h3>';
            exit;
        }
        $i = 1;
        foreach ($habitaciones as $habitacion) {
            if ($i == 1) {
                $pisos_base_datos = [];
                $pisos_reales = [];
                $cualquiera = json_decode(json_encode($habitacion), true);
                foreach (array_keys($cualquiera) as $key) {
                    if (substr($key, 0, 4) == 'piso') {
                        $pisos_base_datos[] = $key;
                    }
                }
                foreach ($pisos_base_datos as $piso) {
                    if ($habitacion->$piso != null and $habitacion != '') {
                        $pisos_reales[] = $piso;
                    }
                }
            }
            $i++;
        }
        $html = view('sabanas.templates.punto21', compact('pisos_reales', 'habitaciones', 'respuestas','pregunta'))->render();
        return response()->json([
            'status' => true,
            'html' => $html,
            'message' => 'Se genero el codigo bien.',
        ]);
    }

    public function getsabanas19()
    {
        $idApp = 16;
        $punto = 19;
        $titulo_pregunta = "Numero de habitacion";
        $correo = \Auth::user()->email;

        $query = "SELECT id_sucursal FROM tb_sucursal WHERE id_sucursal=(SELECT id_sucursal FROM tb_usuario WHERE correo='$correo' LIMIT 1) LIMIT 1";
        $idSucursal = DB::select($query);
        foreach ($idSucursal as $ids) {
            $idsuc = $ids->id_sucursal;
        }
        $query = "SELECT * FROM tb_habitaciones WHERE id_sucursal='$idsuc'";
        $habitaciones = DB::select($query);
        $query = "SELECT id_encuesta FROM tb_encuesta_bloque WHERE id_encuesta IN (SELECT id_encuesta FROM tb_encuesta WHERE id_app='$idApp') AND numero='$punto' LIMIT 1";
        $encuesta = DB::select($query);
        foreach ($encuesta as $enc) {
            $idEncuesta = $enc->id_encuesta;
        }
        $query = "SELECT id_bloque FROM tb_encuesta_bloque WHERE id_encuesta='$idEncuesta' AND numero='$punto' LIMIT 1";
        $bloque = DB::select($query);
        foreach ($bloque as $bloq) {
            $idBloque = $bloq->id_bloque;
        }
        $idPregunta = '12237';
        $query = "
            SELECT
            clave_registro,
            respuesta,
            MAX(SUBSTR(fecha,1,10)) as fecha
            FROM tb_respuesta
            WHERE sucursal='$idsuc'
            AND idcuestionario='$idEncuesta'
            AND idbloque='$idBloque'
            AND idpregunta='$idPregunta'
            AND respuesta IS NOT NULL
            AND respuesta <> ''
            GROUP BY respuesta
            ORDER BY fecha DESC
        ";

        $respuestas = DB::select($query);

        if (!$habitaciones) {
            return '<h3>No hay habitaciones registradas</h3>';
            exit;
        }
        $i = 1;
        foreach ($habitaciones as $habitacion) {
            if ($i == 1) {
                $pisos_base_datos = [];
                $pisos_reales = [];
                $cualquiera = json_decode(json_encode($habitacion), true);
                foreach (array_keys($cualquiera) as $key) {
                    if (substr($key, 0, 4) == 'piso') {
                        $pisos_base_datos[] = $key;
                    }
                }
                foreach ($pisos_base_datos as $piso) {
                    if ($habitacion->$piso != null and $habitacion != '') {
                        $pisos_reales[] = $piso;
                    }
                }
            }
            $i++;
        }
        $html = view('sabanas.templates.punto19', compact('pisos_reales', 'habitaciones', 'respuestas', 'punto', 'idEncuesta', 'idBloque', 'idsuc'))->render();
        return response()->json([
            'status' => true,
            'html' => $html,
            'message' => 'Se genero el codigo bien.',
        ]);
    }

    public function getlimpieza()
    {
        $hostlimpieza = '162.248.52.79';
        $userlimpieza = 'UserCaOaSuMapp';
        $passwordlimpieza = 'Cctv*2022';
        $dblimpieza = 'ibis_limpieza';
        $idApp = 16;
        $correo = \Auth::user()->email;
        $query = "SELECT sucursal, id_sucursal as id FROM tb_sucursal WHERE id_sucursal=(SELECT id_sucursal FROM tb_usuario WHERE correo='$correo' LIMIT 1) LIMIT 1";
        $suc = DB::select($query);
        foreach ($suc as $sucs) {
            $sucursal = $sucs;
        }
        $query = "SELECT * FROM tb_habitaciones WHERE id_sucursal='" . $sucursal->id . "'";
        $habitaciones = DB::select($query);
        $conexionlimpieza = @mysqli_connect($hostlimpieza, $userlimpieza, $passwordlimpieza, $dblimpieza);
        if (!$conexionlimpieza) {
            return "<h1>No se conecto</h1>";
        }
        $tickets = $conexionlimpieza->query("
                SELECT
                cuarto,
                MAX(fechaCheck) as fecha
                FROM limpieza
                WHERE hotel='" . $sucursal->sucursal . "'
                GROUP BY cuarto
                ORDER BY fechaCheck DESC
                ") or die($conexionlimpieza->error);

        if (!$habitaciones) {
            return '<h3>No hay habitaciones registradas</h3>';
            exit;
        }
        $i = 1;
        foreach ($habitaciones as $habitacion) {
            if ($i == 1) {
                $pisos_base_datos = [];
                $pisos_reales = [];
                $cualquiera = json_decode(json_encode($habitacion), true);
                foreach (array_keys($cualquiera) as $key) {
                    if (substr($key, 0, 4) == 'piso') {
                        $pisos_base_datos[] = $key;
                    }
                }
                foreach ($pisos_base_datos as $piso) {
                    if ($habitacion->$piso != null and $habitacion != '') {
                        $pisos_reales[] = $piso;
                    }
                }
            }
            $i++;
        }
        $html = view('sabanas.templates.limpieza', compact('pisos_reales', 'habitaciones', 'tickets'))->render();
        return response()->json([
            'status' => true,
            'html' => $html,
            'message' => 'Se genero el codigo bien.',
        ]);
    }

    public static function nombrePiso(String $nombrePiso)
    {
        $longitudNombre = strlen($nombrePiso);
        $nuevoNombre = 'Piso ' . substr($nombrePiso, 4, $longitudNombre);
        return $nuevoNombre;
    }

    public static function obtenerUltimaFecha($habitacion, $respuestas)
    {
        foreach ($respuestas as $respuesta) {
            if ($respuesta->respuesta == $habitacion) {
                return Carbon::parse($respuesta->fecha)->format('d-m-Y');
            }
        }

        return 'SIN RESPUESTA';
    }

    public static function obtenerUltimaFechat($habitacion, $tickets)
    {
        foreach ($tickets as $ticket) {
            if ($ticket['cuarto'] == $habitacion) {
                return Carbon::parse($ticket['fecha'])->format('d-m-Y');
            }
        }

        return 'SIN RESPUESTA';
    }


    public static function colorFecha($fecha)
    {
        if ($fecha == 'SIN RESPUESTA') {
            return 'text-danger';
        }

        $mesesTranscurridos = Carbon::createFromFormat('d-m-Y', $fecha)->diffInMonths() + 1;
        if ($mesesTranscurridos < 5) {
            return 'text-success';
        }

        if ($mesesTranscurridos >= 5 and $mesesTranscurridos <= 6) {
            return 'text-warning';
        }

        if ($mesesTranscurridos > 6) {
            return 'text-danger';
        }
    }

    public static function colorFechat($fecha)
    {
        if ($fecha == 'SIN RESPUESTA') {
            return 'text-danger';
        }

        $mesesTranscurridos = Carbon::createFromFormat('d-m-Y', $fecha)->diffInMonths() + 1;
        if ($mesesTranscurridos <= 1) {
            return 'text-success';
        }

        if ($mesesTranscurridos > 1 and $mesesTranscurridos <= 2) {
            return 'text-warning';
        }

        if ($mesesTranscurridos > 2) {
            return 'text-danger';
        }
    }

    public function getPreguntaEsp(Request $request)
    {
        $idSucursal = $request->idSucursal;
        $idPregunta = $request->idPregunta;
        $respuestasHabitaciones = $request->respuestasHabitaciones;
        $idEncuesta = $request->idEncuesta;
        $idBloque = $request->idBloque;
        $correo = \Auth::user()->email;
        $punto = 19;

        $query = "SELECT * FROM tb_habitaciones WHERE id_sucursal='$idSucursal'";
        $habitaciones = DB::select($query);
        $query = "SELECT c_tipo_pregunta as tipo, c_titulo_pregunta as titulo FROM tb_encuesta_pregunta WHERE id_pregunta='$idPregunta' AND id_encuesta='$idEncuesta' AND id_bloque='$idBloque' LIMIT 1";
        $pre = DB::select($query);
        foreach ($pre as $p) {
            $pregunta = $p;
        }
        $query = "SELECT A.carpeta FROM tb_usuario as U INNER JOIN tb_app as A ON A.id_app=U.id_app WHERE U.correo='$correo'";
        $pre = DB::select($query);
        foreach ($pre as $p) {
            $carpeta = $p;
        }

        $queryRespuestasPreguntas = "SELECT clave_registro, ";

        if ($pregunta->tipo == 'Evidencia') {
            $tipoPregunta = 'evidencia';
            $queryRespuestasPreguntas .= "evidencia as '$tipoPregunta', ";
        } else {
            $tipoPregunta = 'respuesta';
            $queryRespuestasPreguntas .= "respuesta as '$tipoPregunta', ";
        }

        $queryRespuestasPreguntas .= "
            MAX(SUBSTR(fecha,1,10)) as fecha
            FROM tb_respuesta
            WHERE clave_registro IN ('" . implode("', '", array_column($respuestasHabitaciones, 'clave_registro')) . "')
            AND idpregunta='$idPregunta'
            AND idcuestionario='$idEncuesta'
            AND idbloque='$idBloque'
            AND $tipoPregunta IS NOT NULL
            AND $tipoPregunta <> ''
            GROUP BY clave_registro
            ORDER BY fecha DESC
        ";

        $respuestasPreguntas = DB::select($queryRespuestasPreguntas);

        $newRespuestasPreguntas = [];
        foreach ($respuestasPreguntas as $respuestaPregunta) {
            foreach ($respuestasHabitaciones as $respuestaHabitacion) {
                if ($respuestaPregunta->clave_registro == $respuestaHabitacion['clave_registro']) {
                    $newRespuestasPreguntas[] = [
                        'habitacion' => $respuestaHabitacion['respuesta'],
                        'fecha' => $respuestaHabitacion['fecha'],
                        'respuesta' => $respuestaPregunta->$tipoPregunta
                    ];
                }
            }
        }

        $i = 1;
        foreach ($habitaciones as $habitacion) {
            if ($i == 1) {
                $pisos_base_datos = [];
                $pisos_reales = [];
                $cualquiera = json_decode(json_encode($habitacion), true);
                foreach (array_keys($cualquiera) as $key) {
                    if (substr($key, 0, 4) == 'piso') {
                        $pisos_base_datos[] = $key;
                    }
                }
                foreach ($pisos_base_datos as $piso) {
                    if ($habitacion->$piso != null and $habitacion != '') {
                        $pisos_reales[] = $piso;
                    }
                }
            }
            $i = $i + 1;
        }

        $html = view('sabanas.templates.puntoesp', compact('pisos_reales', 'habitaciones', 'newRespuestasPreguntas', 'punto', 'idEncuesta', 'idBloque', 'idSucursal','carpeta','pregunta'))->render();
        return response()->json([
            'status' => true,
            'html' => $html,
            'message' => 'Se genero el codigo bien.',
        ]);
    }

    public static function obtenerpr(Int $punto, Int $idEncuesta, Int $idBloque)
    {
        $self = new Self;
        switch ($punto) {
            case 9: //Lectura energeticos
                return SabanasController::consultaPunto9($idEncuesta, $idBloque);
                break;

            default:
                return SabanasController::consultaGeneral($idEncuesta, $idBloque);
                break;
        }
    }

    public static function consultaGeneral(Int $idEncuesta, Int $idBloque, String $OrderBy = 'ASC')
    {
        $query = "
            SELECT
                id_pregunta as id,
                c_titulo_pregunta as titulo,
                c_tipo_pregunta as tipo
            FROM tb_encuesta_pregunta
            WHERE c_tipo_pregunta!='Separador'
            AND id_encuesta='$idEncuesta'
            AND id_bloque='$idBloque'
            ORDER BY n_orden_pregunta $OrderBy
        ";

        $consulta = DB::select($query);
        $resultadoFinal = [];
        foreach ($consulta as $fila) {
            array_push($resultadoFinal, $fila);
        }
        return $resultadoFinal;
    }

    public static function consultaPunto9(Int $idEncuesta, Int $idBloque, String $OrderBy = 'ASC')
    {
        $query = "
            SELECT
                id_pregunta as id,
                c_titulo_pregunta as titulo,
                c_tipo_pregunta as tipo
            FROM tb_encuesta_pregunta
            WHERE c_tipo_pregunta!='Separador'
            AND id_encuesta='$idEncuesta'
            AND id_bloque='$idBloque'
            ORDER BY n_orden_pregunta $OrderBy
        ";

        $consulta = DB::select($query);
        $resultadoFinal = [];
        foreach ($consulta as $fila) {
            array_push($resultadoFinal, $fila);
        }
        return $resultadoFinal;
    }

    /**
     * Obtiene la ultima respuesta registrada o un valor 'SIN REVISAR'
     */
    public static function obtenerUltimaRespuesta($habitacion, $newRespuestasPreguntas)
    {
        foreach ($newRespuestasPreguntas as $respuesta) {
            if ($respuesta['habitacion'] == $habitacion) {
                return [
                    'fecha' => Carbon::parse($respuesta['fecha'])->format('d-m-Y'),
                    'respuesta' => $respuesta['respuesta']
                ];
            }
        }

        return [
            'fecha' => 'SIN REVISAR',
            'respuesta' => 'SIN REVISAR'
        ];
    }

    public static function nombrePisoEsp(String $nombrePiso)
    {
        $longitudNombre = strlen($nombrePiso);
        $nuevoNombre = 'Piso ' . substr($nombrePiso, 4, $longitudNombre);
        return $nuevoNombre;
    }

    /**
     * Obtiene la ultima respuesta registrada o un valor 'SIN REVISAR'
     */
    public static function obtenerUltimaRespuestaEsp($habitacion, $newRespuestasPreguntas)
    {
        foreach ($newRespuestasPreguntas as $respuesta) {
            if ($respuesta['habitacion'] == $habitacion) {
                return [
                    'fecha' => Carbon::parse($respuesta['fecha'])->format('d-m-Y'),
                    'respuesta' => $respuesta['respuesta']
                ];
            }
        }

        return [
            'fecha' => 'SIN REVISAR',
            'respuesta' => 'SIN REVISAR'
        ];
    }

    /**
     * retorna la clase de color para la fecha
     */
    public static function colorFechaesp($fecha)
    {
        if ($fecha == 'SIN REVISAR') {
            return 'text-danger';
        }

        $mesesTranscurridos = Carbon::createFromFormat('d-m-Y', $fecha)->diffInMonths() + 1;
        if ($mesesTranscurridos < 5) {
            return 'text-success';
        }

        if ($mesesTranscurridos >= 5 and $mesesTranscurridos <= 6) {
            return 'text-warning';
        }

        if ($mesesTranscurridos > 6) {
            return 'text-danger';
        }
    }
}
