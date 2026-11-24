<div class="form-group">
    <label for="kode">Kode kegiatan</label>
    <input id="kode" name="kode" type="text" value="{{ old('kode', $kegiatan?->getKode()) }}" maxlength="20" required>
    @error('kode')<p class="field-error">{{ $message }}</p>@enderror
</div>
<div class="form-group">
    <label for="nama">Nama kegiatan</label>
    <input id="nama" name="nama" type="text" value="{{ old('nama', $kegiatan?->getNama()) }}" maxlength="100" required>
    @error('nama')<p class="field-error">{{ $message }}</p>@enderror
</div>
<div class="form-group">
    <label for="kategori">Kategori</label>
    <input id="kategori" name="kategori" type="text" value="{{ old('kategori', $kegiatan?->getKategori()) }}" maxlength="50" required>
    @error('kategori')<p class="field-error">{{ $message }}</p>@enderror
</div>
<div class="form-group">
    <label for="tanggal">Tanggal</label>
    <input id="tanggal" name="tanggal" type="date" value="{{ old('tanggal', $kegiatan?->getTanggal()) }}" required>
    @error('tanggal')<p class="field-error">{{ $message }}</p>@enderror
</div>
<div class="form-group">
    <label for="lokasi">Lokasi</label>
    <input id="lokasi" name="lokasi" type="text" value="{{ old('lokasi', $kegiatan?->getLokasi()) }}" maxlength="150" required>
    @error('lokasi')<p class="field-error">{{ $message }}</p>@enderror
</div>
<div class="form-group">
    <label for="status">Status</label>
    <select id="status" name="status" required>
        <option value="">-- Pilih status --</option>
        <option value="Akan datang" @selected(old('status', $kegiatan?->getStatus()) === 'Akan datang')>Akan datang</option>
        <option value="Selesai" @selected(old('status', $kegiatan?->getStatus()) === 'Selesai')>Selesai</option>
    </select>
    @error('status')<p class="field-error">{{ $message }}</p>@enderror
</div>
<div class="form-group">
    <label for="deskripsi">Deskripsi</label>
    <textarea id="deskripsi" name="deskripsi" maxlength="1000" required>{{ old('deskripsi', $kegiatan?->getDeskripsi()) }}</textarea>
    @error('deskripsi')<p class="field-error">{{ $message }}</p>@enderror
</div>