@extends('admin/layouts/default')
@section('title')
    Lịch sử sưu tra @parent
@stop
@section('header_styles')
    {{-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /> --}}
    <link rel="stylesheet" href="{{ asset('assets/vendors/select2/css/select2.min.css') }}">
    <style>
        .qksao {
            font-weight: bold;
            color: red;
        }

        .qkbtn {
            font-weight: bold;
            font-size: 14px !important;
        }

        .qkmodel {
            background-color: #1a67a3 !important;
        }

        .qkbtn {
            font-weight: bold;
            font-size: 14px !important;
        }

        .btn2 {
            font-weight: 500;
            background-color: white !important;
            color: #1a67a3 !important;
            font-size: 14px !important;
        }

        .nqkright {
            text-align: right !important;
            font-size: 14px !important;
            font-weight: 500;
        }
    </style>
@section('content')
    <section class="content">
       
                    <a href="{{ route('suutralogIndex', ['suutra_id' => json_decode($log->log_content)->st_id]) }}"
                        type="cancel" class="btn btn-secondary qkbtn">Quay lại</a>
             
        <div class="row">
           <div class="col-lg-6">
            <h3>Nội dung trước khi cập nhật</h3>
            <div class="row bctk-scrollable-list" style="overflow-x: hidden; height: calc(100vh - 100px) ;">
                <table id="noi-bo-table" class="table-bordered">
                    <tbody>
    
                        <tr >
                            <td style="width: 20%">Công chứng viên</td>
                            <td>{{ json_decode($log->log_content)->ccv_master }}</td>
                        </tr>
                        <tr>
                        <tr>
                            <td>Mã hợp đồng</td>
                            <td>{{ json_decode($log->log_content)->so_hd }}</td>
    
                            </td>
                        <tr>
                            <td>Tên hợp đồng</td>
                            <td>{{ json_decode($log->log_content)->ten_hd }}</td>
                        <tr>
                            <td style="width: 20%">Số công văn</td>
                            <td>{{ json_decode($log->log_content)->so_hd }}</td>
                        </tr>
                        <tr>
                            <td>Đương sự</td>
                            <td>{{ json_decode($log->log_content)->duong_su }}</td>
                        </tr>
                        <tr>
                            <td style="width: 20%">Nội dung</td>
                            {{-- <td style="white-space: pre-line">{!!$log->contract_content!!}</td> --}}
                            <td style="white-space: pre-line">{{ json_decode($log->log_content)->texte }}</td>
                        </tr>
                        <tr>
                            <td style="width: 20%">Ngày thực hiện</td>
                            <td>{{ \Illuminate\Support\Carbon::parse(json_decode($log)->updated_at)->format('d/m/Y') }}</td>
                        </tr>
                    <tr>
                        <td style="width: 20%">File truoc cap nhat (cong van) </td>
    
                                @if (json_decode($log->log_content)->picture)
                                @php
                                        $files = json_decode(json_decode($log->log_content)->picture,true);
                                @endphp
                                @foreach ($files as $key => $img)
                                            @php
                                          $name = json_decode(json_decode($log->log_content)->real_name,true )[$key]
                                    @endphp
                                    @if ($name)
                                 <td style="text-align: center">
                                    <span>{{ json_decode(json_decode($log->log_content)->real_name,true )[$key] }}</span>
    
                                    <a href="{{ route('downloadImg', ['img' => $img, 'name' => $name]) }}"><span><i class="fa fa-download"></i></span></a>
                                    </td>
                                     @endif
                                 @endforeach
                                @endif
                            </td>
                    </tr>
                    <tr>
                        <td style="width: 20%">File truoc cap nhat (giai toa) </td>
                                @if (json_decode($log->log_content)->release_doc_number)
                                @php
                                        $files = json_decode(json_decode($log->log_content)->release_file_path,true);
                                @endphp
                                @foreach ($files as $key => $img)
                                            @php
                                          $name = json_decode(json_decode($log->log_content)->release_file_name,true )[$key]
                                    @endphp
                                    @if ($name)
                                 <td style="text-align: center">
                                    <span>{{ json_decode(json_decode($log->log_content)->release_file_name,true )[$key] }}</span>
    
                                    <a href="{{ route('downloadImg', ['img' => $img, 'name' => $name]) }}"><span><i class="fa fa-download"></i></span></a>
                                    </td>
                                    @else
                                    <td
                                    style="text-align: center">
                                    <span>{{ json_decode(json_decode($log->log_content)->release_file_name,true )[$key] ?? 'FileGiaiToa' }}</span>
                                    <a
                                        href="{{ route('downloadImg', ['img' => $img, 'name' => 'FileGiaiToa.pdf']) }}"><span><i
                                                class="fa fa-download"></i></span></a>
                                </td>
                                @endif
                                 @endforeach
                                @endif
                            </td>
                    </tr>
                    </tbody>
                </table>
            
            </div>
           </div>
           <div class="col-lg-6">
            <h3>Nội dung hiện tại</h3>
            <div class="row bctk-scrollable-list" style="overflow-x: hidden; height: calc(100vh - 100px) ;">
                <table id="noi-bo-table" class="table-bordered  ">
                    <tbody>
    
                        <tr>
                            <td style="width: 20%">Công chứng viên</td>
                            <td>{{ $hoso->ccv_master }}</td>
                        </tr>
                        <tr>
                        <tr>
                            <td>Mã hợp đồng</td>
                            <td>{{ $hoso->so_hd }}</td>
    
                            </td>
                        <tr>
                            <td>Tên hợp đồng</td>
                            <td>{{ $hoso->ten_hd }}</td>
                        <tr>
                            <td style="width: 20%">Số công văn</td>
                            <td>{{ $hoso->so_hd }}</td>
                        </tr>
                        <tr>
                            <td>Đương sự</td>
                            <td>{{ $hoso->duong_su }}</td>
                        </tr>
                        <tr>
                            <td style="width: 20%">Nội dung</td>
                            {{-- <td style="white-space: pre-line">{!!$log->contract_content!!}</td> --}}
                            <td style="white-space: pre-line">{{ $hoso->texte }}</td>
                        </tr>
                        <tr>
                            <td style="width: 20%">Ngày thực hiện</td>
                            <td>{{ \Illuminate\Support\Carbon::parse(json_decode($hoso)->updated_at)->format('d/m/Y') }}</td>
                        </tr>
                    <tr>
                        <td style="width: 20%">File cong van </td>
    
                                @if ($hoso->picture)
                                @php
                                        $files = json_decode($hoso->picture,true);
                                @endphp
                                @foreach ($files as $key => $img)
                                            @php
                                          $name = json_decode($hoso->real_name,true )[$key]
                                    @endphp
                                    @if ($name)
                                 <td style="text-align: center">
                                    <span>{{ json_decode($hoso->real_name,true )[$key] }}</span>
    
                                    <a href="{{ route('downloadImg', ['img' => $img, 'name' => $name]) }}"><span><i class="fa fa-download"></i></span></a>
                                    </td>
                                     @endif
                                 @endforeach
                                @endif
                            </td>
                    </tr>
                    <tr>
                        <td style="width: 20%">File giai toa </td>
                                @if ($hoso->release_doc_number)
                                @php
                                        $files = json_decode($hoso->release_file_path,true);
                                @endphp
                                @foreach ($files as $key => $img)
                                            @php
                                            
                                          $name = json_decode($hoso->release_file_name,true )[$key]
                                    @endphp
                                    @if ($name)
                                 <td style="text-align: center">
                                    <span>{{ json_decode($hoso->release_file_name,true )[$key] }}</span>
    
                                    <a href="{{ route('downloadImg', ['img' => $img, 'name' => $name]) }}"><span><i class="fa fa-download"></i></span></a>
                                    </td>
                                     @else
                                     <td
                                     style="text-align: center">
                                     <span>{{ json_decode($hoso->release_file_name,true )[$key] ?? 'FileGiaiToa' }}</span>
                                     <a
                                         href="{{ route('downloadImg', ['img' => $img, 'name' => 'FileGiaiToa.pdf']) }}"><span><i
                                                 class="fa fa-download"></i></span></a>
                                 </td>
                                 @endif
                                 @endforeach
                                @endif
                            </td>
                    </tr>
                    </tbody>
                </table>
            
            </div>
           </div>
        </div>
      
    </section>
@stop
