<div
    class="ck-sisa-waktu {{ $sisaDetik <= 300 ? 'ck-sisa-waktu-genting' : '' }}"
    x-data="{
        sisa: {{ $sisaDetik }},
        total: {{ $totalDetik }},
        pad(n) { return String(n).padStart(2, '0') },
        jam() { return this.pad(Math.floor(this.sisa / 3600)) },
        menit() { return this.pad(Math.floor((this.sisa % 3600) / 60)) },
        detik() { return this.pad(this.sisa % 60) },
        persen() {
            if (this.total <= 0) {
                return 0;
            }
            return Math.max(0, Math.min(100, (this.sisa / this.total) * 100));
        },
    }"
    x-init="
        const tick = () => {
            if (sisa <= 0) {
                $wire.kumpulkan();
                return;
            }
            sisa--;
            if (sisa <= 0) {
                $wire.kumpulkan();
                return;
            }
            setTimeout(tick, 1000);
        };
        if (sisa <= 0) {
            $wire.kumpulkan();
        } else {
            setTimeout(tick, 1000);
        }
    "
    :class="{ 'ck-sisa-waktu-genting': sisa <= 300 }"
>
    <p class="ck-hint mb-1">Sisa waktu</p>
    <p class="ck-sisa-waktu-angka mb-0">
        <span x-text="jam()"></span>:<span x-text="menit()"></span>:<span x-text="detik()"></span>
    </p>
    <span class="ck-sisa-garis" aria-hidden="true">
        <span
            class="ck-sisa-garis-isi"
            style="width: {{ $totalDetik > 0 ? round(($sisaDetik / $totalDetik) * 100, 2) : 0 }}%"
            :style="'width: ' + persen() + '%'"
        ></span>
    </span>
</div>
