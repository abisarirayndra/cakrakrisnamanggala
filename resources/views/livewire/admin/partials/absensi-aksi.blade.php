<div class="d-flex justify-content-end gap-2">
    <button type="button" class="btn btn-sm btn-ck-ghost" wire:click="ubahAbsensi('{{ $tipe }}', {{ $row->id }})" wire:loading.attr="disabled">
        Ubah
    </button>
    <button
        type="button"
        class="btn btn-sm btn-outline-danger rounded-pill"
        wire:click="hapusAbsensi('{{ $tipe }}', {{ $row->id }})"
        wire:confirm="Hapus absensi ini?"
        wire:loading.attr="disabled"
    >
        <span wire:loading wire:target="hapusAbsensi('{{ $tipe }}', {{ $row->id }})" class="spinner-border spinner-border-sm me-1" aria-hidden="true"></span>
        Hapus
    </button>
</div>
