<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Client\ConnectionException;
use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    public function getAsrList(Request $request): JsonResponse
    {
        try {
            $durationCallMin = $request->input('duration_call_min');
            $durationCallMax = $request->input('duration_call_max');

            $result = DB::connection('sqlsrv1')->select('SET NOCOUNT ON; EXEC GetAsrList ?, ?', [
                $durationCallMin,
                $durationCallMax,
            ]);

            DB::connection('pgsql')->transaction(function () use ($result) {
                foreach ($result as $row) {
                    $commutationId = $row->_CommutationId;
                    $directionCommutationId = $row->_DirectionCommutationId;
                    $sourceCommutationId = $row->_SourceCommutationId;

                    DB::connection('pgsql')->statement('CALL addasrentry(?, ?, ?)', [
                        $commutationId, $directionCommutationId, $sourceCommutationId
                    ]);
                }
            });

            return response()->json(['data' => $result]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error occurred while getting ASR list: ' . $e->getMessage()], 400);
        } catch (ConnectionException $e) {
            return response()->json(['message' => 'Error occurred while connecting to the database: ' . $e->getMessage()], 400);
        }
    }

    public function getAsrEntry()
    {
        try {
            $result = DB::connection('pgsql')->select('SELECT * FROM getasrentry()');

            return response()->json(['data' => $result]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error occurred while getting ASR entry: ' . $e->getMessage()], 400);
        }
    }

    public function updateAsrEntry(Request $request)
    {
        try {
            $commutationId = $request->input('_CommutationId');
            $leftChannel = $request->input('_LeftChannel');
            $rightChannel = $request->input('_RightChannel');
            $allChannel = $request->input('_AllChannel');

            DB::connection('pgsql')->statement('CALL updateasrentry(?, ?, ?, ?)', [
                $commutationId,
                $leftChannel,
                $rightChannel,
                $allChannel,
            ]);

            return response()->json(['data' => ['success' => true]]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error occurred while updating ASR entry: ' . $e->getMessage()], 400);
        }
    }

    public function getAsrResult(string $idCommutation)
    {
        try {
            $result = DB::connection('pgsql')->select('SELECT * FROM getasrresult(?)', [$idCommutation]);
            return response()->json([
                'leftChannel' => $result[0]->jsonleftchannel,
                'rightChannel' => $result[0]->jsonrightchannel
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error occurred while getting ASR results: ' . $e->getMessage()], 400);
        }
    }

    public function exportData()
    {
        try {
            $results = DB::connection('pgsql')->select('SELECT * FROM exporttosca()');
            foreach ($results as $result) {
                DB::connection('sqlsrv1')->statement('SET NOCOUNT ON; EXEC AddAsrEntry ?, ?, ?, ?, ?', [
                    $result->idcommutation,
                    $result->jsonleftchannel,
                    $result->jsonrightchannel,
                    $result->iddirectioncommutation,
                    $result->idsourcecommutation
                ]);
            }
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
