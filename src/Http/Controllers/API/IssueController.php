<?php

namespace Visiosoft\Kanban\Http\Controllers\API;

use Visiosoft\Kanban\Models\Issue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;

class IssueController extends BaseController
{
    /**
     * @OA\Get(
     *      path="/api/kanban/issues",
     *      operationId="getIssuesList",
     *      tags={"Issues"},
     *      summary="Get list of issues",
     *      description="Returns list of issues",
     *      @OA\Parameter(
     *          name="board_id",
     *          in="query",
     *          description="Filter by board ID",
     *          required=false,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Parameter(
     *          name="status",
     *          in="query",
     *          description="Filter by status",
     *          required=false,
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(
     *          name="priority",
     *          in="query",
     *          description="Filter by priority",
     *          required=false,
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(
     *          name="assigned_to",
     *          in="query",
     *          description="Filter by assigned user ID",
     *          required=false,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Parameter(
     *          name="search",
     *          in="query",
     *          description="Search in title and description",
     *          required=false,
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Issue")),
     *              @OA\Property(property="message", type="string", example="Issues retrieved successfully.")
     *          )
     *       )
     * )
     */
    public function index(Request $request)
    {
        $query = Issue::query();

        if ($request->has('board_id')) {
            $query->where('board_id', $request->board_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->has('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $issues = $query->get();
        return $this->sendResponse($issues, 'Issues retrieved successfully.');
    }

    /**
     * @OA\Post(
     *      path="/api/kanban/issues",
     *      operationId="storeIssue",
     *      tags={"Issues"},
     *      summary="Store new issue",
     *      description="Returns issue data",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/IssueStoreRequest")
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", ref="#/components/schemas/Issue"),
     *              @OA\Property(property="message", type="string", example="Issue created successfully.")
     *          )
     *       ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation Error"
     *      )
     * )
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'title' => 'required',
            'board_id' => 'required|exists:boards,id',
            'status' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 422);       
        }

        $issue = Issue::create($input);

        return $this->sendResponse($issue, 'Issue created successfully.');
    }

    /**
     * @OA\Get(
     *      path="/api/kanban/issues/{id}",
     *      operationId="getIssueById",
     *      tags={"Issues"},
     *      summary="Get information about issue",
     *      description="Returns issue data",
     *      @OA\Parameter(
     *          name="id",
     *          description="Issue id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", ref="#/components/schemas/Issue"),
     *              @OA\Property(property="message", type="string", example="Issue retrieved successfully.")
     *          )
     *       ),
     *      @OA\Response(
     *          response=404,
     *          description="Issue not found"
     *      )
     * )
     */
    public function show($id)
    {
        $issue = Issue::find($id);

        if (is_null($issue)) {
            return $this->sendError('Issue not found.');
        }

        return $this->sendResponse($issue, 'Issue retrieved successfully.');
    }

    /**
     * @OA\Put(
     *      path="/api/kanban/issues/{id}",
     *      operationId="updateIssue",
     *      tags={"Issues"},
     *      summary="Update existing issue",
     *      description="Returns updated issue data",
     *      @OA\Parameter(
     *          name="id",
     *          description="Issue id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/IssueUpdateRequest")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", ref="#/components/schemas/Issue"),
     *              @OA\Property(property="message", type="string", example="Issue updated successfully.")
     *          )
     *       ),
     *      @OA\Response(
     *          response=404,
     *          description="Issue not found"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation Error"
     *      )
     * )
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'title' => 'required',
            'board_id' => 'exists:boards,id',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 422);       
        }

        $issue = Issue::find($id);
        if (is_null($issue)) {
            return $this->sendError('Issue not found.');
        }

        $issue->update($input);

        return $this->sendResponse($issue, 'Issue updated successfully.');
    }


    /**
     * @OA\Post(
     *      path="/api/kanban/issues/{id}/change-priority",
     *      operationId="changeIssuePriority",
     *      tags={"Issues"},
     *      summary="Change issue priority",
     *      description="Updates the priority of an issue",
     *      @OA\Parameter(
     *          name="id",
     *          description="Issue id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"priority"},
     *              @OA\Property(property="priority", type="string", example="high")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", ref="#/components/schemas/Issue"),
     *              @OA\Property(property="message", type="string", example="Priority updated successfully.")
     *          )
     *       )
     * )
     */
    public function changePriority(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'priority' => 'required|string',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $issue = Issue::find($id);
        if (is_null($issue)) {
            return $this->sendError('Issue not found.');
        }

        $issue->priority = $request->priority;
        $issue->save();

        return $this->sendResponse($issue, 'Priority updated successfully.');
    }

    /**
     * @OA\Post(
     *      path="/api/kanban/issues/{id}/change-assignee",
     *      operationId="changeIssueAssignee",
     *      tags={"Issues"},
     *      summary="Change issue assignee",
     *      description="Updates the assignee of an issue",
     *      @OA\Parameter(
     *          name="id",
     *          description="Issue id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"assigned_to"},
     *              @OA\Property(property="assigned_to", type="integer", example=1)
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", ref="#/components/schemas/Issue"),
     *              @OA\Property(property="message", type="string", example="Assignee updated successfully.")
     *          )
     *       )
     * )
     */
    public function changeAssignee(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'assigned_to' => 'required|integer',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $issue = Issue::find($id);
        if (is_null($issue)) {
            return $this->sendError('Issue not found.');
        }

        $issue->assigned_to = $request->assigned_to;
        $issue->save();

        return $this->sendResponse($issue, 'Assignee updated successfully.');
    }

    /**
     * @OA\Post(
     *      path="/api/kanban/issues/{id}/change-due-date",
     *      operationId="changeIssueDueDate",
     *      tags={"Issues"},
     *      summary="Change issue due date",
     *      description="Updates the due date of an issue",
     *      @OA\Parameter(
     *          name="id",
     *          description="Issue id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"due_date"},
     *              @OA\Property(property="due_date", type="string", format="date", example="2023-12-31")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", ref="#/components/schemas/Issue"),
     *              @OA\Property(property="message", type="string", example="Due date updated successfully.")
     *          )
     *       )
     * )
     */
    public function changeDueDateTime(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'due_date' => 'required|date',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $issue = Issue::find($id);
        if (is_null($issue)) {
            return $this->sendError('Issue not found.');
        }

        $issue->due_date = $request->due_date;
        $issue->save();

        return $this->sendResponse($issue, 'Due date updated successfully.');
    }

    /**
     * @OA\Post(
     *      path="/api/kanban/issues/{id}/change-start-date",
     *      operationId="changeIssueStartDate",
     *      tags={"Issues"},
     *      summary="Change issue start date",
     *      description="Updates the start date of an issue",
     *      @OA\Parameter(
     *          name="id",
     *          description="Issue id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"start_at"},
     *              @OA\Property(property="start_at", type="string", format="date-time", example="2023-12-31 10:00:00")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", ref="#/components/schemas/Issue"),
     *              @OA\Property(property="message", type="string", example="Start date updated successfully.")
     *          )
     *       )
     * )
     */
    public function changeStartDateTime(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'start_at' => 'required|date',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $issue = Issue::find($id);
        if (is_null($issue)) {
            return $this->sendError('Issue not found.');
        }

        $issue->start_at = $request->start_at;
        $issue->save();

        return $this->sendResponse($issue, 'Start date updated successfully.');
    }

    /**
     * @OA\Post(
     *      path="/api/kanban/issues/{id}/change-status",
     *      operationId="changeIssueStatus",
     *      tags={"Issues"},
     *      summary="Change issue status",
     *      description="Updates the status of an issue",
     *      @OA\Parameter(
     *          name="id",
     *          description="Issue id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"status"},
     *              @OA\Property(property="status", type="string", example="done")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", ref="#/components/schemas/Issue"),
     *              @OA\Property(property="message", type="string", example="Status updated successfully.")
     *          )
     *       )
     * )
     */
    public function changeStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $issue = Issue::find($id);
        if (is_null($issue)) {
            return $this->sendError('Issue not found.');
        }

        $issue->status = $request->status;
        $issue->save();

        return $this->sendResponse($issue, 'Status updated successfully.');
    }

    /**
     * @OA\Get(
     *      path="/api/kanban/issue/users",
     *      operationId="getIssueUsers",
     *      tags={"Issues"},
     *      summary="Get list of users",
     *      description="Returns list of users with id and name",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(
     *                      type="object",
     *                      @OA\Property(property="id", type="integer", example=1),
     *                      @OA\Property(property="name", type="string", example="John Doe")
     *                  )
     *              ),
     *              @OA\Property(property="message", type="string", example="Users retrieved successfully.")
     *          )
     *       )
     * )
     */
    public function users()
    {
        $userModel = config('auth.providers.users.model');
        $users = $userModel::select('id', 'name')->get();

        return $this->sendResponse($users, 'Users retrieved successfully.');
    }
}
