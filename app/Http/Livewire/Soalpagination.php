<?php

namespace App\Http\Livewire;

use App\Soal;
use Livewire\Component;
use Livewire\WithPagination;

class Soalpagination extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public function render()
    {
        $tema = request()->query('q');
        $soal = Soal::where('tema_id', $tema)->inRandomOrder()->paginate(5);

        return view('livewire.soalpagination', compact('soal'));
    }
}
