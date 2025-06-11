@extends('layouts.db')

@section('title', 'Kelola Voucher')
@section('content')
    <h1 class="main-title">Kelola Voucher</h1>
    <div class="subtitle-row">
        <div class="main-subtitle">Daftar-Daftar Voucher</div>
        <div class="content-actions">
            <button class="search-btn" title="Cari">
                <svg width="20" height="20" fill="none">
                    <path d="M19 19l-4.35-4.35M9 16a7 7 0 1 0 0-14 7 7 0 0 0 0 14Z" stroke="#232323" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
            <button class="add-btn">
                <img src="images/tambah-db.png" alt="Tambah" class="add-btn-icon">
                <span>Tambah Voucher</span>
            </button>
        </div>
    </div>
    <div class="table-container">
        <table class="voucher-table">
            <thead>
                <tr>
                    <th>KODE VOUCHER</th>
                    <th>JENIS</th>
                    <th>NOMINAL</th>
                    <th>MINIMAL TRANSAKSI</th>
                    <th>BERLAKU DARI</th>
                    <th>BERLAKU SAMPAI</th>
                    <th>STATUS</th>
                    <th>AKSI</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><b>GARUDA10</b></td>
                    <td>Persentase</td>
                    <td>10%</td>
                    <td>Rp500.000</td>
                    <td>01-06-2025</td>
                    <td>30-06-2025</td>
                    <td><span class="badge aktif">Aktif</span></td>
                    <td>
                        <button class="action-btn" title="Edit"><img src="images/edit.png" alt="Edit"
                                class="icon-btn"></button>
                        <button class="action-btn" title="Delete"><img src="images/delete.png" alt="Delete"
                                class="icon-btn"></button>
                    </td>
                </tr>
                <tr>
                    <td><b>HEMAT50K</b></td>
                    <td>Nominal</td>
                    <td>Rp50.000</td>
                    <td>Rp1.000.000</td>
                    <td>01-06-2025</td>
                    <td>15-06-2025</td>
                    <td><span class="badge tidak">Tidak Aktif</span></td>
                    <td>
                        <button class="action-btn" title="Edit"><img src="images/edit.png" alt="Edit"
                                class="icon-btn"></button>
                        <button class="action-btn" title="Delete"><img src="images/delete.png" alt="Delete"
                                class="icon-btn"></button>
                    </td>
                </tr>
                <tr>
                    <td><b>WELCOME25</b></td>
                    <td>Persentase</td>
                    <td>25%</td>
                    <td>Rp0</td>
                    <td>01-06-2025</td>
                    <td>31-12-2025</td>
                    <td><span class="badge aktif">Aktif</span></td>
                    <td>
                        <button class="action-btn" title="Edit"><img src="images/edit.png" alt="Edit"
                                class="icon-btn"></button>
                        <button class="action-btn" title="Delete"><img src="images/delete.png" alt="Delete"
                                class="icon-btn"></button>
                    </td>
                </tr>
            </tbody>
        </table>
        <!-- Modal Tambah Voucher -->
        <div id="addVoucherModal" class="modal" style="display:none;">
            <div class="modal-content">
                <span class="close-btn" id="addModalCloseBtn">&times;</span>
                <h2>Tambah Voucher</h2>
                <form id="addVoucherForm">
                    <label for="addKode">Kode Voucher:</label>
                    <input type="text" id="addKode" name="kode" required />

                    <label for="addJenis">Jenis:</label>
                    <select id="addJenis" name="jenis" required>
                        <option value="Persentase">Persentase</option>
                        <option value="Nominal">Nominal</option>
                    </select>

                    <label for="addNominal">Nominal:</label>
                    <input type="number" id="addNominal" name="nominal" min="0" required />

                    <label for="addMinTransaksi">Minimal Transaksi:</label>
                    <input type="number" id="addMinTransaksi" name="min_transaksi" min="0" required />

                    <label for="addBerlakuDari">Berlaku Dari:</label>
                    <input type="date" id="addBerlakuDari" name="berlaku_dari" required />

                    <label for="addBerlakuSampai">Berlaku Sampai:</label>
                    <input type="date" id="addBerlakuSampai" name="berlaku_sampai" required />

                    <div class="modal-actions">
                        <button type="submit" class="btn-save">Simpan</button>
                        <button type="button" class="btn-cancel" id="addModalCancelBtn">Batal</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Edit Voucher -->
        <div id="editVoucherModal" class="modal" style="display:none;">
            <div class="modal-content">
                <span class="close-btn" id="editModalCloseBtn">&times;</span>
                <h2>Edit Voucher</h2>
                <form id="editVoucherForm">
                    <label for="editKode">Kode Voucher:</label>
                    <input type="text" id="editKode" name="kode" readonly />

                    <label for="editJenis">Jenis:</label>
                    <select id="editJenis" name="jenis" required>
                        <option value="Persentase">Persentase</option>
                        <option value="Nominal">Nominal</option>
                    </select>

                    <label for="editNominal">Nominal:</label>
                    <input type="number" id="editNominal" name="nominal" min="0" required />

                    <label for="editMinTransaksi">Minimal Transaksi:</label>
                    <input type="number" id="editMinTransaksi" name="min_transaksi" min="0" required />

                    <label for="editBerlakuDari">Berlaku Dari:</label>
                    <input type="date" id="editBerlakuDari" name="berlaku_dari" required />

                    <label for="editBerlakuSampai">Berlaku Sampai:</label>
                    <input type="date" id="editBerlakuSampai" name="berlaku_sampai" required />

                    <div class="modal-actions">
                        <button type="submit" class="btn-save">Simpan</button>
                        <button type="button" class="btn-cancel" id="editModalCancelBtn">Batal</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Modal elemen
            const addModal = document.getElementById('addVoucherModal');
            const editModal = document.getElementById('editVoucherModal');

            // Tombol buka modal tambah
            const addBtn = document.querySelector('.add-btn');
            const addCloseBtn = document.getElementById('addModalCloseBtn');
            const addCancelBtn = document.getElementById('addModalCancelBtn');

            // Tombol tutup modal edit
            const editCloseBtn = document.getElementById('editModalCloseBtn');
            const editCancelBtn = document.getElementById('editModalCancelBtn');

            // Tombol edit pada tabel
            const editButtons = document.querySelectorAll('.voucher-table .action-btn[title="Edit"]');

            // Buka modal tambah
            addBtn.addEventListener('click', () => {
                addModal.style.display = 'flex';
            });

            // Tutup modal tambah
            addCloseBtn.addEventListener('click', () => {
                addModal.style.display = 'none';
            });
            addCancelBtn.addEventListener('click', () => {
                addModal.style.display = 'none';
            });

            // Tutup modal edit
            editCloseBtn.addEventListener('click', () => {
                editModal.style.display = 'none';
            });
            editCancelBtn.addEventListener('click', () => {
                editModal.style.display = 'none';
            });

            // Tutup modal jika klik di luar modal-content
            window.addEventListener('click', (e) => {
                if (e.target === addModal) addModal.style.display = 'none';
                if (e.target === editModal) editModal.style.display = 'none';
            });

            // Buka modal edit dan isi data dari baris tabel
            editButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const row = button.closest('tr');
                    document.getElementById('editKode').value = row.children[0].textContent.trim();
                    document.getElementById('editJenis').value = row.children[1].textContent.trim();
                    // Hilangkan simbol Rp atau % dan titik ribuan untuk nominal dan minimal transaksi
                    const nominalText = row.children[2].textContent.trim().replace(/[Rp.%\s]/g, '').replace(/\./g, '');
                    const minTransText = row.children[3].textContent.trim().replace(/[Rp.%\s]/g, '').replace(/\./g, '');
                    document.getElementById('editNominal').value = nominalText || 0;
                    document.getElementById('editMinTransaksi').value = minTransText || 0;
                    // Tanggal berlaku dari dan sampai (format dd-mm-yyyy ke yyyy-mm-dd)
                    function formatDateDMYtoYMD(dateStr) {
                        const parts = dateStr.split('-');
                        if (parts.length === 3) return `${parts[2]}-${parts[1]}-${parts[0]}`;
                        return '';
                    }
                    document.getElementById('editBerlakuDari').value = formatDateDMYtoYMD(row.children[4].textContent.trim());
                    document.getElementById('editBerlakuSampai').value = formatDateDMYtoYMD(row.children[5].textContent.trim());

                    // Status
                    const statusSpan = row.children[6].querySelector('span');
                    document.getElementById('editStatus').value = statusSpan.classList.contains('aktif') ? 'aktif' : 'tidak';

                    editModal.style.display = 'flex';
                });
            });
        });
    </script>

@endpush