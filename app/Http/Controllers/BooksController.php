<?php

namespace App\Http\Controllers;

use App\Book;
use App\Traits\ApiResponserTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Books Controller.
 */
class BooksController extends Controller
{
    use ApiResponserTrait;

    /**
     * Returns the list of books.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->successResponse(Book::all());
    }

    /**
     * Shows a given book.
     *
     * @param int $book
     *
     * @return \Illuminate\Http\Response
     */
    public function show(int $book)
    {
        return $this->successResponse(Book::findOrFail($book));
    }

    /**
     * Creates a new book.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|max:255',
            'gender' => 'required|in:male,female',
            'country' => 'required|max:255',
        ];

        $data = $this->validate($request, $rules);

        return $this->successResponse(
            Book::create($data),
            Response::HTTP_CREATED
        );
    }

    /**
     * Updates a given book.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $book
     *
     * @return void
     */
    public function update(Request $request, int $book)
    {
        $rules = [
            'name' => 'max:255',
            'gender' => 'in:male,female',
            'country' => 'max:255',
        ];

        $data = $this->validate($request, $rules);

        $model = Book::findOrFail($book);

        $model->fill($data);

        if ($model->isClean()) {
            return $this->errorResponse(
                'At least one value must be changed.',
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $model->save();

        return $this->successResponse($model);
    }

    /**
     * Deletes a given book.
     *
     * @param int $book
     *
     * @return void
     */
    public function destroy(int $book)
    {
        Book::findOrFail($book)->delete();

        return $this->successResponse();
    }
}
