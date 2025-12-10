<?php

namespace Visiosoft\Kanban\Http\Controllers\API;

use Visiosoft\Kanban\Models\Board;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;

class BoardController extends BaseController
{
    /**
     * @OA\Get(
     *      path="/api/kanban/boards",
     *      operationId="getBoardsList",
     *      tags={"Boards"},
     *      summary="Get list of boards",
     *      description="Returns list of boards",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Board")),
     *              @OA\Property(property="message", type="string", example="Boards retrieved successfully.")
     *          )
     *       )
     * )
     */
    public function index()
    {
        $boards = Board::all();
        return $this->sendResponse($boards, 'Boards retrieved successfully.');
    }

    /**
     * @OA\Post(
     *      path="/api/kanban/boards",
     *      operationId="storeBoard",
     *      tags={"Boards"},
     *      summary="Store new board",
     *      description="Returns board data",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/BoardStoreRequest")
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", ref="#/components/schemas/Board"),
     *              @OA\Property(property="message", type="string", example="Board created successfully.")
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
            'name' => 'required',
            'order' => 'integer'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 422);       
        }

        $board = Board::create($input);

        return $this->sendResponse($board, 'Board created successfully.');
    }

    /**
     * @OA\Get(
     *      path="/api/kanban/boards/{id}",
     *      operationId="getBoardById",
     *      tags={"Boards"},
     *      summary="Get information about board",
     *      description="Returns board data",
     *      @OA\Parameter(
     *          name="id",
     *          description="Board id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *           @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", ref="#/components/schemas/Board"),
     *              @OA\Property(property="message", type="string", example="Board retrieved successfully.")
     *          )
     *       ),
     *      @OA\Response(
     *          response=404,
     *          description="Board not found"
     *      )
     * )
     */
    public function show($id)
    {
        $board = Board::find($id);

        if (is_null($board)) {
            return $this->sendError('Board not found.');
        }

        return $this->sendResponse($board, 'Board retrieved successfully.');
    }

    /**
     * @OA\Put(
     *      path="/api/kanban/boards/{id}",
     *      operationId="updateBoard",
     *      tags={"Boards"},
     *      summary="Update existing board",
     *      description="Returns updated board data",
     *      @OA\Parameter(
     *          name="id",
     *          description="Board id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/BoardUpdateRequest")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *           @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", ref="#/components/schemas/Board"),
     *              @OA\Property(property="message", type="string", example="Board updated successfully.")
     *          )
     *       ),
     *      @OA\Response(
     *          response=404,
     *          description="Board not found"
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
            'name' => 'required',
            'order' => 'integer'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 422);       
        }

        $board = Board::find($id);
        if (is_null($board)) {
            return $this->sendError('Board not found.');
        }

        $board->update($input);

        return $this->sendResponse($board, 'Board updated successfully.');
    }


}
