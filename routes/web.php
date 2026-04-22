<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
use Illuminate\Support\Facades\Route as Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
	return "TEST BERHASIL";
});

Auth::routes();
Route::get('invoice-pemakaian-bahan/{pelanggan}/{keperluan}/{periode}/{tglmulai}/{tglakhir}', 'PemakaianBahanController@invoicePemakaian');
// Route::group(['middleware' => ['auth']], function() {
Route::get('/', 'HomeController@index')->name('home');
Route::get('/change-password', 'HomeController@showChangePasswordForm');
Route::post('/change-password', 'HomeController@changePassword')->name('change-password');

//Alat
Route::post('alat/tambah-alat', 'AlatController@store')->name('Alat.store');
Route::post('/alat/{id}/update', 'AlatController@update')->name('Alat.update');
Route::resource('alat', 'AlatController');

//Peminjaman Alat
Route::get('pinjam-alat/tambah', 'PeminjamanAlatController@create');
Route::post('pinjam-alat/tambah', 'PeminjamanAlatController@store')->name('PeminjamanAlat.store');
Route::post('/pinjam-alat/{id}/update', 'PeminjamanAlatController@update')->where('id', '(.*)')->name('PeminjamanAlat.update');
Route::delete('/pinjam-alat/{id}', 'PeminjamanAlatController@destroy')->where('id', '(.*)');
Route::get('pinjam-alat', 'PeminjamanAlatController@index');
Route::get('pinjam-alat/usulan', 'PeminjamanAlatController@indexUsulanSemua')->name('PeminjamanAlat.indexUsulanSemua');
Route::get('pinjam-alat/usulanku', 'PeminjamanAlatController@indexUsulanku')->name('PeminjamanAlat.indexUsulanku');
Route::get('/pinjam-alat/{id}', 'PeminjamanAlatController@getInfo')->where('id', '(.*)');
Route::post('/pinjam-alat/cekstok', 'PeminjamanAlatController@cekstok')->name('PeminjamanAlat.cekstok');

//Detail Peminjaman Alat
Route::get('pinjam-alat-detail/tambah-detail/{id}', 'DetailPeminjamanAlatController@tambahDetail')->where('id', '(.*)');
Route::get('pinjam-alat-detail/{id}/getinfo', 'DetailPeminjamanAlatController@getInfo')->where('id', '(.*)');
Route::get('pinjam-alat-detail/{id}', 'DetailPeminjamanAlatController@show')->where('id', '(.*)');
Route::post('pinjam-alat-detail/tambah', 'DetailPeminjamanAlatController@store')->name('DetailPeminjamanAlat.store');
Route::post('/pinjam-alat-detail/{id}/update', 'DetailPeminjamanAlatController@update')->name('DetailPeminjamanAlat.update');
Route::post('/pinjam-alat-detail/updateBanyakData', 'DetailPeminjamanAlatController@updateBanyakData')->name('DetailPeminjamanAlat.updateBanyakData');
Route::post('/kembali-alat/{id}/kembali', 'DetailPeminjamanAlatController@kembali')->name('DetailPeminjamanAlat.kembali');
Route::post('/kembali-alat/kembaliBanyakData', 'DetailPeminjamanAlatController@kembaliBanyakData')->name('DetailPeminjamanAlat.kembaliBanyakData');
Route::resource('pinjam-alat-detail', 'DetailPeminjamanAlatController');
Route::post('pinjam-alat/updatedata', 'DetailPeminjamanAlatController@updatedata')->name('DetailPeminjamanAlat.updateData');
Route::post('pinjam-alat/updatedataverif', 'DetailPeminjamanAlatController@updatedataverif')->name('DetailPeminjamanAlat.updateDataVerif');
Route::post('/kembali-alat/kembali', 'DetailPeminjamanAlatController@kembali2')->name('DetailPeminjamanAlat.kembali2');

Route::delete('/hapus-riwayat/{id}', 'DetailPengembalianAlatController@destroy');

//Bahan
Route::post('bahan/tambah-bahan', 'BahanController@store')->name('Bahan.store');
Route::post('/bahan/{id}/update', 'BahanController@update')->name('Bahan.update');
Route::get('/bahan/{id}/detail', 'BahanController@getDetailBahanPemakaian')->name('bahan.getDetailBahanPemakaian');
Route::resource('bahan', 'BahanController');

//Pemakaian Bahan
Route::get('pakai-bahan/tambah', 'PemakaianBahanController@create');
Route::post('pakai-bahan/tambah', 'PemakaianBahanController@store')->name('PemakaianBahan.store');
Route::get('/pakai-bahan/{id}', 'PemakaianBahanController@getInfo')->where('id', '(.*)');
Route::post('/pakai-bahan/{id}/update', 'PemakaianBahanController@update')->where('id', '(.*)')->name('PemakaianBahan.update');
Route::delete('/pakai-bahan/{id}', 'PemakaianBahanController@destroy')->where('id', '(.*)');
Route::get('pakai-bahan', 'PemakaianBahanController@index');
//Route::get('pakai-bahan/usulanku', 'PemakaianBahanController@indexUsulanku')->name('PemakaianBahan.indexUsulanku');

Route::get('usulan-pemakaian-bahan', 'PemakaianBahanController@indexUsulanku')->name('PemakaianBahan.indexUsulanku');
Route::post('/pakai-alat/cekstok', 'PemakaianBahanController@cekstok')->name('PemakaianBahan.cekstok');

//Detail Pemakaian Bahan
Route::get('/pakai-bahan-detail/get-info-detail/{id}', 'DetailPemakaianBahanController@getInfo');
Route::get('pakai-bahan-detail/tambah-detail/{id}', 'DetailPemakaianBahanController@tambahDetail')->where('id', '(.*)');
Route::get('pakai-bahan-detail/{id}', 'DetailPemakaianBahanController@show')->where('id', '(.*)');
Route::post('pakai-bahan-detail/tambah-detail', 'DetailPemakaianBahanController@store')->name('DetailPemakaianBahan.store');
Route::post('/pakai-bahan-detail/detail/{id}/update', 'DetailPemakaianBahanController@update')->name('DetailPemakaianBahan.update');
Route::post('/pakai-bahan-detail/updateBanyakData', 'DetailPemakaianBahanController@updateBanyakData')->name('DetailPemakaianBahanController.updateBanyakData');
Route::post('/pakai-bahan-detail/updatedata', 'DetailPemakaianBahanController@updatedata')->name('DetailPemakaianBahan.updateData');
Route::post('/pakai-bahan-detail/updatedataverif', 'DetailPemakaianBahanController@updatedataverif')->name('DetailPemakaianBahan.updateDataVerif');
Route::delete('/pakai-bahan-detail/hapus-detail/{id}', 'DetailPemakaianBahanController@destroy');
Route::get('pakai-bahan-detail/detail', 'DetailPemakaianBahanController@index');
Route::get('invoice-pemakaian-bahan-pertransaksi/{no_transaksi}', 'PemakaianBahanController@invoicePemakaianPerTransaksi')->where('no_transaksi', '(.*)');
Route::get('laporan-pemakaian-bahan-pertahun/{tahun}', 'PemakaianBahanController@cetakTotalPemakaianPerTahun')->where('tahun', '(.*)');
Route::get('laporan-pemakaian-bahan-periode/{periode}', 'PemakaianBahanController@cetakTotalPemakaianPerPeriode')->where('periode', '(.*)');
// Route::resource('pakai-bahan-detail', 'DetailPemakaianBahanController');
Route::post('/pinjam-alat-detail/editfee', 'DetailPemakaianBahanController@editfee')->name('DetailPemakaianBahan.editfee');

//Pembelian Alat
Route::get('/beli-alat/get-harga/{id}', 'PembelianAlatController@getHarga');
Route::get('/beli-alat/get-info/{id}', 'PembelianAlatController@show')->where('id', '(.*)');
Route::get('beli-alat/tambah', 'PembelianAlatController@create');
Route::post('beli-alat/tambah', 'PembelianAlatController@store')->name('PembelianAlat.store');
Route::get('/beli-alat/{id}/edit', 'PembelianAlatController@edit')->where('id', '(.*)');
Route::post('/beli-alat/{id}/update', 'PembelianAlatController@update')->name('PembelianAlat.update')->where('id', '(.*)');
Route::delete('/beli-alat/{id}', 'PembelianAlatController@destroy')->where('id', '(.*)');
Route::get('beli-alat', 'PembelianAlatController@index');

//Detail Pembelian Alat
Route::get('beli-alat-detail/get-info-detail/{id}', 'DetailPembelianAlatController@getInfoDetail');
Route::get('beli-alat-detail/tambah-detail/{id}', 'DetailPembelianAlatController@tambahDetail')->where('id', '(.*)');
Route::get('beli-alat-detail/{noPO}', 'DetailPembelianAlatController@show')->where('noPO', '(.*)');
Route::get('beli-alat-detail/tambah', 'DetailPembelianAlatController@create');
Route::post('beli-alat-detail/tambah', 'DetailPembelianAlatController@store')->name('DetailPembelianAlat.store');
Route::get('/beli-alat-detail/{id}/edit', 'DetailPembelianAlatController@edit');
Route::post('/beli-alat-detail/{id}/update', 'DetailPembelianAlatController@update')->name('DetailPembelianAlat.update');
Route::delete('beli-alat-detail/hapus-detail/{id}', 'DetailPembelianAlatController@destroy');
Route::get('beli-alat-detail', 'DetailPembelianAlatController@index');

//Penerimaan Bahan
Route::get('/terima-bahan/get-harga/{id}', 'PenerimaanBahanController@getHarga');
Route::get('/terima-bahan/get-info/{id}', 'PenerimaanBahanController@show')->where('id', '(.*)');
Route::get('terima-bahan/tambah', 'PenerimaanBahanController@create');
Route::post('terima-bahan/tambah', 'PenerimaanBahanController@store')->name('PenerimaanBahan.store');
Route::get('/terima-bahan/{id}/edit', 'PenerimaanBahanController@edit')->where('id', '(.*)');
Route::post('/terima-bahan/{id}/update', 'PenerimaanBahanController@update')->name('PenerimaanBahan.update')->where('id', '(.*)');
Route::delete('/terima-bahan/{id}', 'PenerimaanBahanController@destroy')->where('id', '(.*)');
Route::get('terima-bahan', 'PenerimaanBahanController@index');
// Route::resource('terima-bahan', 'PenerimaanBahanController');

//Detail Penerimaan Bahan
Route::get('terima-bahan-detail/get-info-detail/{id}', 'DetailPenerimaanBahanController@getInfoDetail');
Route::get('terima-bahan-detail/tambah-detail/{id}', 'DetailPenerimaanBahanController@tambahDetail')->where('id', '(.*)');
Route::get('terima-bahan-detail/{noPO}', 'DetailPenerimaanBahanController@show')->where('noPO', '(.*)');
Route::get('terima-bahan-detail/tambah', 'DetailPenerimaanBahanController@create');
Route::post('terima-bahan-detail/tambah', 'DetailPenerimaanBahanController@store')->name('DetailPenerimaanBahan.store');
Route::get('/terima-bahan-detail/{id}/edit', 'DetailPenerimaanBahanController@edit');
Route::post('/terima-bahan-detail/{id}/update', 'DetailPenerimaanBahanController@update')->name('DetailPenerimaanBahan.update');
Route::delete('terima-bahan-detail/hapus-detail/{id}', 'DetailPenerimaanBahanController@destroy');
Route::get('terima-bahan-detail', 'DetailPenerimaanBahanController@index');
// Route::resource('terima-bahan-detail', 'DetailPenerimaanBahanController');

//Laporan Pemakaian Alat
Route::get('laporan-peminjaman-alat/', 'PeminjamanAlatController@laporan');
Route::get('invoice-peminjaman-alat/{pelanggan}/{keperluan}/{periode}', 'PeminjamanAlatController@invoicePeminjaman')->name('PeminjamanAlat.invoice');
Route::get('peminjaman-alat/{id}', 'PeminjamanAlatController@show');
Route::get('alat-tidakterpakai', 'PeminjamanAlatController@alatTidakTerpakai');
Route::get('preview-peminjaman-alat/{pelanggan}/{keperluan}/{periode}', 'PeminjamanAlatController@previewPeminjaman')->name('PeminjamanAlat.preview');
Route::get('accLaboran/alat/{id}', 'PeminjamanAlatController@AccLaboran')->where('id', '(.*)');
Route::get('accKoordinator/alat/{id}/{pelanggan}/{keperluan}/{periode}', 'PeminjamanAlatController@AccKoordinator')->where('id', '(.*)');
Route::get('accKalab/alat/{id}/{pelanggan}/{keperluan}/{periode}', 'PeminjamanAlatController@AccKalab')->where('id', '(.*)');

//Laporan Pemakaian Bahan
Route::get('laporan-pemakaian-bahan/', 'PemakaianBahanController@laporan');
Route::get('laporan-pemakaian-bahan/laporanku', 'PemakaianBahanController@laporanku');

Route::get('pemakaian-bahan/{id}/{tglmulai}/{tglakhir}', 'PemakaianBahanController@show');
Route::get('pemakaian-bahan/pernota/{id}/{tglmulai}/{tglakhir}', 'PemakaianBahanController@showPernota');
Route::get('accLaboran/{id}', 'PemakaianBahanController@AccLaboran')->where('id', '(.*)');
Route::get('accKoordinator/{id}/{pelanggan}/{keperluan}/{periode}', 'PemakaianBahanController@AccKoordinator')->where('id', '(.*)');
Route::get('accKalab/{id}/{pelanggan}/{keperluan}/{periode}', 'PemakaianBahanController@AccKalab')->where('id', '(.*)');
Route::get('uploadBuktiPembayaran/{id}', 'PemakaianBahanController@uploadBuktiPembayaran')->where('id', '(.*)');
Route::post('bukti/{id}', 'PemakaianBahanController@bukti')->where('id', '(.*)')->name('PemakaianBahan.bukti');
Route::get('preview-pemakaian-bahan/{pelanggan}/{keperluan}/{periode}', 'PemakaianBahanController@previewPemakaian');
Route::post('reject-pemakaian-bahan/{id}', 'PemakaianBahanController@rejectPemakaian')->where('id', '(.*)')->name('PemakaianBahan.reject');
Route::post('ganti-pemakaian-bahan/{id}', 'PemakaianBahanController@gantiPemakaian')->where('id', '(.*)')->name('PemakaianBahan.ganti');
// Route::get('reject-pemakaian-bahan/{id}/{pesan}/{kekurangan}','PemakaianBahanController@rejectPemakaian2')->where('id', '(.*)');
//Laporan Total Pemakaian Bahan
Route::get('total-pemakaian-bahan/{tahun}', 'PemakaianBahanController@pemakaianTahun');
Route::get('total-pemakaian', 'PemakaianBahanController@totalPemakaian');
Route::get('downloadbukti/{gambar}', 'PemakaianBahanController@downloadBukti')->where('gambar', '(.*)');
Route::get('updateStatusApproval/{id}', 'PemakaianBahanController@updateStatusApproval')->where('id', '(.*)');

Route::get('total-pemakaian-bahan-periode/{periode}', 'PemakaianBahanController@pemakaianPeriode');
Route::get('total-pemakaian-periode', 'PemakaianBahanController@totalPemakaianPeriode');

Route::get('bahan-tidakterpakai-pertahun/{tahun}', 'PemakaianBahanController@tidakTerpakaiTahun');
Route::get('bahan-tidakterpakai', 'PemakaianBahanController@bahanTidakTerpakaiPertahun');
Route::get('bahan-tidakterpakai-perperiode/{periode}', 'PemakaianBahanController@tidakTerpakaiPeriode');
Route::get('bahan-tidakterpakaiperiode', 'PemakaianBahanController@bahanTidakTerpakaiPeriode');

//Cek minimum stok
Route::get('minstok-bahan', 'BahanController@minStok');

// Route::get('no-trans','PemakaianBahanController@noTransaksiBaru');

//Lainnya
Route::prefix('lainnya')->name('lainnya.')->group(function () {
	//Jenis Alat
	Route::post('jenis-alat/tambah-jenis', 'JenisAlatController@store')->name('JenisAlat.store');
	Route::get('/jenis-alat/{id}', 'JenisAlatController@show');
	Route::get('/jenis-alat/{id}/edit', 'JenisAlatController@edit');
	Route::post('/jenis-alat/{id}/update', 'JenisAlatController@update')->name('JenisAlat.update')->where('id', '(.*)');
	;
	Route::resource('jenis-alat', 'JenisAlatController');

	//Jenis Bahan
	Route::post('jenis-bahan/tambah-jenis', 'JenisBahanController@store')->name('JenisBahan.store');
	Route::get('/jenis-bahan/{id}', 'JenisBahanController@show');
	Route::get('/jenis-bahan/{id}/edit', 'JenisBahanController@edit');
	Route::post('/jenis-bahan/{id}/update', 'JenisBahanController@update')->name('JenisBahan.update');
	Route::resource('jenis-bahan', 'JenisBahanController');

	//Merek Alat
	Route::post('merek/tambah-merek', 'MerekController@store')->name('Merek.store');
	Route::get('/merek/{id}', 'MerekController@show');
	Route::get('/merek/{id}/edit', 'MerekController@edit');
	Route::post('/merek/{id}/update', 'MerekController@update')->name('Merek.update');
	Route::resource('merek', 'MerekController');

	//Merek bahan
	Route::post('merekBahan/tambah-merek', 'MerekBahanController@store')->name('MerekBahan.store');
	Route::get('/merekBahan/{id}', 'MerekBahanController@show');
	Route::get('/merekBahan/{id}/edit', 'MerekBahanController@edit');
	Route::post('/merekBahan/{id}/update', 'MerekBahanController@update')->name('MerekBahan.update');
	Route::resource('merekBahan', 'MerekBahanController');

	//Supplier
	Route::post('supplier/tambah-supplier', 'SupplierController@store')->name('Supplier.store');
	Route::get('/supplier/{id}', 'SupplierController@show');
	Route::get('/supplier/{id}/edit', 'SupplierController@edit');
	Route::post('/supplier/{id}/update', 'SupplierController@update')->name('Supplier.update');
	Route::resource('supplier', 'SupplierController');

	//Keperluan
	Route::post('keperluan/tambah-keperluan', 'KeperluanController@store')->name('Keperluan.store');
	Route::get('/keperluan/{id}', 'KeperluanController@show');
	Route::get('/keperluan/{id}/edit', 'KeperluanController@edit');
	Route::post('/keperluan/{id}/update', 'KeperluanController@update')->name('Keperluan.update');
	Route::resource('keperluan', 'KeperluanController');

	//Pelanggan
	Route::get('pelanggan/tambah-pelanggan', 'PelangganController@create');
	Route::post('pelanggan/tambah-pelanggan', 'PelangganController@store')->name('Pelanggan.store');
	Route::get('/pelanggan/{id}', 'PelangganController@show');
	Route::get('/pelanggan/{id}/edit', 'PelangganController@edit');
	Route::post('/pelanggan/{id}/update', 'PelangganController@update')->name('PelaFlognggan.update');
	Route::resource('pelanggan', 'PelangganController');

	//Periode
	Route::post('periode/tambah-periode', 'PeriodeController@store')->name('Periode.store');
	Route::get('/periode/{id}', 'PeriodeController@show');
	Route::get('/periode/{id}/edit', 'PeriodeController@edit');
	Route::post('/periode/{id}/update', 'PeriodeController@update')->name('Periode.update');
	Route::resource('periode', 'PeriodeController');

	//Laboran
	Route::post('laboran/tambah-laboran', 'LaboranController@store')->name('Laboran.store');
	Route::get('/laboran/{id}', 'LaboranController@show');
	Route::get('/laboran/{id}/conf', 'LaboranController@getConf');
	Route::post('/laboran/{id}/update', 'LaboranController@update')->name('Laboran.update');
	Route::post('/laboran/{id}/configure', 'LaboranController@configure')->name('Laboran.configure');
	Route::resource('laboran', 'LaboranController');

	//Pejabat Struktural
	Route::post('pejabat/tambah-pejabat', 'PejabatController@store')->name('Pejabat.store');
	Route::get('/pejabat/{id}', 'PejabatController@show');
	Route::post('/pejabat/{id}/update', 'PejabatController@update')->name('Pejabat.update');
	Route::resource('pejabat', 'PejabatController');

	//Laboratorium
	Route::post('laboratorium/tambah-laboratorium', 'LaboratoriumController@store')->name('Lab.store');
	Route::get('/laboratorium/{id}', 'LaboratoriumController@show');
	Route::post('/laboratorium/{id}/update', 'LaboratoriumController@update')->name('Lab.update');
	Route::resource('laboratorium', 'LaboratoriumController');

	//Koordinator
	Route::get('/koordinator/{id}', 'KoordinatorController@show');
	Route::post('/koordinator/{id}/update', 'KoordinatorController@update')->name('Koordinator.update');
	Route::resource('koordinator', 'KoordinatorController');
});
// });

Route::fallback(function () {
	return response()->view('errors.404', [], 404);
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');



