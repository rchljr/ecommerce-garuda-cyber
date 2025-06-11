@extends('layouts.db')

@section('title', 'Kelola Landing Page')
@section('content')
    <h1 class="main-title">Kelola Statistik Landing Page</h1>
    <div class="main-subtitle">Edit data statistik yang tampil di halaman depan</div>
    <div class="table-container">
        <table class="stat-table">
            <thead>
                <tr>
                    <th>NAMA STATISTIK</th>
                    <th>NILAI</th>
                    <th>AKSI</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Members</td>
                    <td><b>2,245,341</b></td>
                    <td>
                        <button class="action-btn" title="Edit">
                            <img src="images/edit.png" alt="Edit" class="icon-btn">
                        </button>
                    </td>
                </tr>
                <tr>
                    <td>Clubs</td>
                    <td><b>46,328</b></td>
                    <td>
                        <button class="action-btn" title="Edit">
                            <img src="images/edit.png" alt="Edit" class="icon-btn">
                        </button>
                    </td>
                </tr>
                <tr>
                    <td>Event Bookings</td>
                    <td><b>828,867</b></td>
                    <td>
                        <button class="action-btn" title="Edit">
                            <img src="images/edit.png" alt="Edit" class="icon-btn">
                        </button>
                    </td>
                </tr>
                <tr>
                    <td>Payments</td>
                    <td><b>1,926,436</b></td>
                    <td>
                        <button class="action-btn" title="Edit">
                            <img src="images/edit.png" alt="Edit" class="icon-btn">
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
        <!-- Modal Edit Nilai Statistik -->
        <div id="editModal" class="modal" style="display: none;">
            <div class="modal-content">
                <span class="close-btn" id="modalCloseBtn">&times;</span>
                <h1>Edit Nilai Statistik</h1>
                <form id="editForm">
                    <label for="statValue">Nilai Baru:</label>
                    <input type="number" id="statValue" name="statValue" min="0" required />
                    <div class="modal-actions">
                        <button type="submit" class="btn-save">Simpan</button>
                        <button type="button" class="btn-cancel" id="modalCancelBtn">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('editModal');
            const modalCloseBtn = document.getElementById('modalCloseBtn');
            const modalCancelBtn = document.getElementById('modalCancelBtn');
            const editForm = document.getElementById('editForm');
            const statValueInput = document.getElementById('statValue');

            // Semua tombol edit
            const editButtons = document.querySelectorAll('.action-btn');

            editButtons.forEach(button => {
                button.addEventListener('click', function () {
                    // Ambil nilai saat ini dari baris tabel (kolom nilai)
                    const row = this.closest('tr');
                    const currentValue = row.querySelector('td:nth-child(2) b').textContent.replace(/,/g, '');
                    statValueInput.value = currentValue;

                    // Tampilkan modal
                    modal.style.display = 'flex';

                    // Fokus input
                    statValueInput.focus();
                });
            });

            // Tutup modal
            modalCloseBtn.addEventListener('click', () => {
                modal.style.display = 'none';
            });
            modalCancelBtn.addEventListener('click', () => {
                modal.style.display = 'none';
            });

            // Tutup modal jika klik di luar modal-content
            window.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.style.display = 'none';
                }
            });
        });
    </script>
@endpush