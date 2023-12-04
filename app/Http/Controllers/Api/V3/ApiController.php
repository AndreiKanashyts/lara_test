<?php

namespace App\Http\Controllers\Api\V3;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    const LoginAuth = "SCA_Integration";
    const PswAuth = "MK0r2Rv1";

    public function addOktellCall(Request $request)
    {
        if ($request->getUser() != self::LoginAuth || $request->getPassword() != self::PswAuth) {
            return response('Доступ запрещен. Неверный логин или пароль', 401);
        }

        $request->validate([
            '*.Id' => 'required',
            '*.IdChain' => 'required',
            '*.TimeStart' => 'required',
            '*.TimeAnswer' => 'required',
            '*.TimeStop' => 'nullable',
            '*.AUserId' => 'required',
            '*.BUserId' => 'required',
            '*.Astr' => 'required',
            '*.Bstr' => 'required',
            '*.ConnectionType' => 'required',
            '*.ReasonStop' => 'nullable',
            '*.StopSide' => 'nullable',
            '*.Succes' => 'required',
        ]);

        try {
            $jsonData = $request->json()->all();
            
            $conn = DB::connection('sqlsrv2');

            foreach ($jsonData as $data) 
            {
                try {
                    $stmt = $conn->getPdo()->prepare("SET NOCOUNT ON; EXEC AddOktellCall @Id = ?, @IdChain = ?, @TimeStart = ?, 
                                            @TimeAnswer = ?, @TimeStop = ?, @AUserId = ?, 
                                            @BUserId = ?, @Astr = ?, @Bstr = ?, @ConnectionType = ?, 
                                            @ReasonStop = ?, @StopSide = ?, @Succes = ?");

                    $stmt->execute([
                        $data['Id'], $data['IdChain'], $data['TimeStart'], $data['TimeAnswer'], $data['TimeStop'],
                        $data['AUserId'], $data['BUserId'], $data['Astr'], $data['Bstr'], $data['ConnectionType'],
                        $data['ReasonStop'], $data['StopSide'], $data['Succes']
                    ]);
                } catch (\Exception $e) {
                    return response('Error executing SQL: '.$e->getMessage(), 500);
                }
            }

            return response('Ok', 200);
        } catch (\Exception $e) {
            return response('Error: '.$e->getMessage(), 500);
        }
    }

    public function getOktellCallNull()
    {
        try {
            $conn = DB::connection('sqlsrv2');

            $stmt2 = $conn->getPdo()->prepare("SET NOCOUNT ON; EXEC GetOktellCallNull");

            $stmt2->execute();

            $result = $stmt2->fetchAll(\PDO::FETCH_ASSOC);

            return response()->json($result, 200);
        } catch (Exception $e) {
            return response('Error: '.$e->getMessage(), 500);
        }
    }
}