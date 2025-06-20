
<section class="py-16 md:py-24 bg-gray-100">
    <div class="container mx-auto px-6">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div>
                <h2
                    class="text-4xl md:text-5xl font-extrabold leading-tight bg-gradient-to-r from-[#B20000] to-[#2B2A4C] bg-clip-text text-transparent">
                    Membantu bisnis UMKM untuk berinovasi kembali
                </h2>
                <p class="mt-4 text-black-600">Melayani dengan tekad dan kerja keras tanpa batas.</p>
            </div>
            <div class="grid grid-cols-2 gap-x-8 gap-y-10">
                <!-- Stat Item 1: Members -->
                <div class="flex items-center gap-4">
                    <div class="flex-shrink-0">
                        <img src="{{ asset('images/members.png') }}" alt="Members" class="stats-icon" />
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-black-600">{{ number_format($stats->total_users ?? 0) }}</p>
                        <p class="text-red-600">Members</p>
                    </div>
                </div>
                <!-- Stat Item 2: Clubs -->
                <div class="flex items-center gap-4">
                    <div class="flex-shrink-0">
                        <img src="{{ asset('images/clubs.png') }}" alt="Clubs" class="stats-icon" />
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-black-600">{{ number_format($stats->total_shops ?? 0) }}</p>
                        <p class="text-red-600">Clubs</p>
                    </div>
                </div>
                <!-- Stat Item 3: Event Bookings -->
                <div class="flex items-center gap-4">
                    <div class="flex-shrink-0">
                        <img src="{{ asset('images/eventbook.png') }}" alt="Event Bookings" class="stats-icon" />
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-black-600">{{ number_format($stats->total_visitors ?? 0) }}
                        </p>
                        <p class="text-red-600">Event Bookings</p>
                    </div>
                </div>
                <!-- Stat Item 4: Payments -->
                <div class="flex items-center gap-4">
                    <div class="flex-shrink-0">
                        <img src="{{ asset('images/payments.png') }}" alt="Payments" class="stats-icon" />
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-black-600">
                            {{ number_format($stats->total_transactions ?? 0) }}</p>
                        <p class="text-red-600">Payments</p>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>
