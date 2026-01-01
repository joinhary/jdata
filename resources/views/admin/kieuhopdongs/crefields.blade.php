<div class="row">
    <label for="nv_ten">Kiểu hợp đồng: (<span class="text-danger qksao">*</span>)</label>
    <input type="text" name="kieu_hd" class="form-control" required>

</div>
<div class="row">
    <label for="nv_ten">ID liên kết:</label>
    <input type="text" name="lien_ket_id" class="form-control" required>

</div>
<br>
<div class="row">
    <a href="{{ route('admin.kieuhopdongs.index') }}" type="cancel" class="btn btn-secondary qkbtn">Hủy</a>
    <button type="submit" class="btn btn-primary qkbtn">Lưu</button>
</div>
