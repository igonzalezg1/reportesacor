@php
use App\Http\Controllers\SabanasController;
@endphp
<style>
    .center-item {
        position: absolute;
        top: 50%;
        left: 50%;
        -ms-transform: translate(-50%, -50%);
        transform: translate(-50%, -50%);
    }

    .text-orange {
        color: #ffa533 !important;
    }

    #tablaSabana {
        font-family: Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    #tablaSabana td,
    #tablaSabana th {
        border: 1px solid #ddd;
        padding: 8px;
    }

    #tablaSabana tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    #tablaSabana th {
        padding-top: 12px;
        padding-bottom: 12px;
        text-align: left;
        background-color: #108dcc;
        color: white;
    }
</style>
<hr>
<div id="sabanaHeader" class="row">
    <div class="col-12 col-md-6">
        <div style="margin: 120px 0px 120px 0px !important;">
            <div class="center-item">
                <h2 style="margin-bottom: -3px;">Punto 19</h2>
                <span>Revisión del 15% de las habitaciones del hotel</span>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6">
        <ol>
            <li>Menor a 5 meses: <span class="text-success"><b>FECHA</b></span></li>
            <li>Entre 5 y 6 Meses: <span class="text-warning"><b>FECHA</b></span></li>
            <li>Mayor a 6 Meses: <span class="text-danger"><b>FECHA</b></span></li>
            <li>No hay registros: <span class="text-danger"><b>SIN RESPUESTA</b></span></li>
        </ol>
    </div>
</div>
<hr>
<div class="my-3">
    <div class="row">
        <div class="col-12">
            @php
                $preguntas = SabanasController::obtenerpr(19, $idEncuesta, $idBloque);

                $respu = str_replace('"', "'", json_encode($respuestas));
            @endphp
            <div class="mx-auto" style="max-width: 500px;">
                <form id="preguntasSabanaForm"
                    onsubmit="filtrarPregunta(<?= $respu ?>, '<?= $idsuc ?>', '<?= $idEncuesta ?>', '<?= $idBloque ?>')">
                    <div class="input-group">
                        <select id="pregunta" name="pregunta" class="custom-select" required>
                            <option value="">Elige una pregunta...</option>
                            @foreach ($preguntas as $pregunta)
                                <option value="{{ $pregunta->id }}">{{ $pregunta->titulo }}</option>
                            @endforeach
                        </select>
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-danger">Filtrar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<hr>
<div id="sabanaBody" class="table-responsive mt-3">
    <table id="tablaSabana">
        <thead>
            <tr>
                @foreach ($pisos_reales as $piso)
                    <th>{{ SabanasController::nombrePiso($piso) }}</th>
                    <th>Fecha</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($habitaciones as $habitacion)
                <tr>
                    @foreach ($pisos_reales as $piso)
                        @php
                            $pisoq = intval($habitacion->$piso);
                        @endphp
                        <td><a
                                href="{{ route('getRespuestas19', ['id_encuesta' => 84, 'id_bloque' => 424, 'punto' => 19, 'piso' => $pisoq]) }}">{{ $habitacion->$piso }}</a>
                        </td>
                        @if ($habitacion->$piso != null and $habitacion->$piso != '')
                            @php
                                $fecha = SabanasController::obtenerUltimaFecha($habitacion->$piso, $respuestas);
                            @endphp
                            <td class="{{ SabanasController::colorFecha($fecha) }}">{{ $fecha }}</td>
                        @else
                            <td></td>
                        @endif
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
