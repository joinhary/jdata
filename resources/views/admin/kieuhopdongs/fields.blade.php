<div class="row">
    <label for="nv_ten">Kiểu hợp đồng: (<span class="text-danger qksao">*</span>)</label>
    {!! \App\Helpers\Form::text('kieu_hd', null, ['class' => 'form-control']) !!}
</div>
<div class="row">
    <label for="nv_ten">ID liên kết: </label>
    {!! \App\Helpers\Form::text('liet_ket_id', $kieuhopdong->lien_ket_id, ['class' => 'form-control']) !!}
</div>
<br>
<div class="row">
    <a href="{{ route('admin.kieuhopdongs.index') }}" type="cancel" class="btn btn-secondary qkbtn">Hủy</a>
    <button type="submit" class="btn btn-primary qkbtn">Cập nhật</button>
</div>
