<?php

namespace App\Http\Controllers\Api\V2;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    public function getAssessments(Request $request): JsonResponse
    {
        try {
            $month = $request->input('month');
            $year = $request->input('year');
            $systemUser = $request->input('system_user');

            $assessments = DB::connection('sqlsrv2')->select('SET NOCOUNT ON; EXEC GetAssessmentUPA ?, ?, ?', [
                $month,
                $year,
                $systemUser,
            ]);

            // Добавление сквозной нумерации
            foreach ($assessments as $index => $assessment) {
                $assessment->number = $index + 1; // Нумерация начинается с 1
            }

            return response()->json(['data' => $assessments]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error occurred while getting Assessments: ' . $e->getMessage()], 500);
        }
    }

    public function getAssessmentCheckList(Request $request): JsonResponse
    {
        try {
            $id = $request->input('id');

            $checklist = DB::connection('sqlsrv1')->select("SET NOCOUNT ON; EXEC GetAssessmentCheckListUPA ?", [$id]);
            
            return response()->json(['data' => $checklist]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error occurred while getting Checklist: ' . $e->getMessage()], 500);
        }
    }

    public function getBadge(Request $request): JsonResponse
    {
        try {
            $systemUser = $request->input('system_user');

            $badge = DB::connection('sqlsrv1')->select("SET NOCOUNT ON; EXEC GetBadgeUPA ?", [$systemUser]);
            
            return response()->json(['data' => $badge]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error occurred while getting Badge: ' . $e->getMessage()], 500);
        }
    }

    public function getAssessmentComment(Request $request): JsonResponse
    {
        try {
            $id = $request->input('id');
            $systemUser = $request->input('system_user');

            $comments = DB::connection('sqlsrv1')->select("SET NOCOUNT ON; EXEC GetAssessmentCommentUPA ?, ?", [$id, $systemUser]);
            
            return response()->json(['data' => $comments]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error occurred while getting Comments: ' . $e->getMessage()], 500);
        }
    }

    public function getFileObject(Request $request): JsonResponse
    {
        try {
            $id = $request->input('id');
            $sectionId = $request->input('section_id');

            $files = DB::connection('sqlsrv1')->select("SET NOCOUNT ON; EXEC GetFileObjectUPA ?, ?", [$id, $sectionId]);
            
            return response()->json(['data' => $files]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error occurred while getting Files: ' . $e->getMessage()], 500);
        }
    }

    public function getLinkedObject(Request $request): JsonResponse
    {
        try {
            $id = $request->input('id');
            $sectionId = $request->input('section_id');

            $linkedObjects = DB::connection('sqlsrv1')->select("SET NOCOUNT ON; EXEC GetLinkedObjectUPA  ?, ?", [$id, $sectionId]);
            
            // Добавление сквозной нумерации
            foreach ($linkedObjects as $index => $linkedObject) {
                $linkedObject->number = $index + 1; // Нумерация начинается с 1
            }

            return response()->json(['data' => $linkedObjects]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error occurred while getting LinkedObjects: ' . $e->getMessage()], 500);
        }
    }

    public function setAssessmentStatus(Request $request): JsonResponse
    {
        try {
            $id = $request->input('id');
            $oldStatus = $request->input('old_status');
            $newStatus = $request->input('new_status');

            DB::connection('sqlsrv1')->statement("SET NOCOUNT ON; EXEC SetAssessmentStatusUPA   ?, ?, ?", [$id, $oldStatus, $newStatus]);
            
            return response()->json(['message' => 'Status updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function addComment(Request $request): JsonResponse
    {
        try {
            $id = $request->input('id');
            $typeCommunication = $request->input('type_communication');
            $comment = $request->input('comment');
            $section = $request->input('section');
            $systemUser = $request->input('system_user');

            DB::connection('sqlsrv1')->statement("SET NOCOUNT ON; EXEC AddCommentUPA   ?, ?, ?, ?, ?", [
                $id,
                $typeCommunication,
                $comment,
                $section,
                $systemUser,
            ]);
            
            return response()->json(['message' => 'Comment added successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}
