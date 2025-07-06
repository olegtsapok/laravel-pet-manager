<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StorePetRequest;
use App\Http\Requests\UpdatePetRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Services\PetstoreService;

class PetController extends Controller
{
    protected $petstore;

    public function __construct(PetstoreService $petstore)
    {
        $this->petstore = $petstore;
    }

    public function index(Request $request)
    {
        $status = $request->input('status', 'available');
        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'asc');

        $allPets = $this->petstore->getPetsByStatus($status);
        $pets = collect($allPets)->sortBy(function ($pet) use ($sort) {
            return $pet[$sort] ?? '';
        }, SORT_REGULAR, $direction === 'desc');

        $perPage = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $pets->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginated = new LengthAwarePaginator(
            $currentItems,
            $pets->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('pets.index', [
            'pets' => $paginated,
            'status' => $status,
            'sort' => $sort,
            'direction' => $direction
        ]);
    }

    public function create()
    {
        return view('pets.create');
    }

    public function store(StorePetRequest $request)
    {
        $data = $this->collectPetDataFromRequest($request);

        if ($this->petstore->createPet($data)) {
            $this->uploadImageToApi($data['id'], $request);
            return redirect()->route('pets.index')->with('success', 'Pet created.');
        }

        return back()->with('error', 'Failed to create pet.');
    }

    public function edit($id)
    {
        $pet = $this->petstore->getPet($id);
        if (!$pet) return redirect()->route('pets.index')->with('error', 'Pet not found.');
        return view('pets.edit', compact('pet'));
    }

    public function update(UpdatePetRequest $request, $id)
    {
        $data = $this->collectPetDataFromRequest($request);

        if ($this->petstore->updatePet($data)) {
            $this->uploadImageToApi($data['id'], $request);
            return redirect()->route('pets.index')->with('success', 'Pet updated.');
        }

        return back()->with('error', 'Failed to update pet.');
    }

    public function destroy($id)
    {
        if ($this->petstore->deletePet($id)) {
            return redirect()->route('pets.index')->with('success', 'Pet deleted.');
        }

        return redirect()->route('pets.index')->with('error', 'Failed to delete pet.');
    }

    private function uploadImageToApi($petId, $request)
    {
        if ($request->hasFile('imageFile')) {
            $file = $request->file('imageFile');
            $this->petstore->uploadImage(
                $petId,
                $file->getPathname(),
                $file->getClientOriginalName(),
                $file->getMimeType()
            );
        }
    }

    private function collectPetDataFromRequest($request, $id = null)
    {
        $photoUrls = array_map('trim', explode(',', $request->input('photoUrls')));

        return [
            'id' => $id ?? now()->timestamp,
            'name' => $request->input('name'),
            'status' => $request->input('status'),
            'category' => ['id' => 0, 'name' => $request->input('category')],
            'tags' => collect(explode(',', $request->input('tags')))->map(fn($tag) => ['id' => 0, 'name' => trim($tag)])->all(),
            'photoUrls' => $photoUrls
        ];
    }
}