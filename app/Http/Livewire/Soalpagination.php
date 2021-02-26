<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Soal;
use Livewire\WithPagination;

class Soalpagination extends Component
{
    use WithPagination;

    public function render()
    {
        $soal = Soal::paginate(1);

        return view('livewire.soalpagination', compact('soal'));
    }
}
