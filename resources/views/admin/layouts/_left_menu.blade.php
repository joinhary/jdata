@php
    $role = Sentinel::check()->user_roles()->first()->slug;
    $vp=\App\Models\NhanVienModel::find(Sentinel::getUser()->id)->nv_vanphong;
@endphp
<div class="col-md-3 left_col"
     style="margin-top: 54px;display: block !important; overflow-x: hidden;height: calc(100vh - 100px);">
    <!-- sidebar menu -->
    <div id="sidebar-menu" class="main_menu_side main_menu">
        <div class="menu_section">
            <ul class="nav side-menu" style="font-weight: 500;margin: 0px">

                @if($vp !== "2190")
                <li {!! (Request::is('admin/customer/*') ? 'class="active"' : '') !!}>
                    <a href="{{ route('indexKhachHang') }}">
                        <i class="fa fa-users"></i>
                        Quản lý đương sự
                    </a>
                </li>
                <li {!! (Request::is('admin/taisan/*') ? 'class="active"' : '') !!}>
                    <a href="{{ route('indexTaiSan') }}">
                        <i class="fa fa-dollar"></i>
                        Quản lý tài sản, đối tượng
                    </a>
                </li>
                @endif
                <li {!! (Request::is('admin/solrsearch/*') ? 'class="active"' : '') !!}>
                    <a href="{{ route('searchSolr') }}" onclick="loadingLeftMenu()">
                            <p class="qkpnew">
                                <i class="fa fa-search"></i>
                                <span class="title">Tra cứu thông tin </span>
                            </p>
                        </a>
                    </li>
                
             
				
				
                {{--                <li {!! (Request::is('admin/ket-qua-hoat-dong/index') || Request::is('admin/suutra/edit/*') ? 'class="active"' : '' ) !!}>--}}
                {{--                    <a href="{{ route('searchViBang') }}">--}}
                {{--                        <p class="qkpnew">--}}
                {{--                            <i class="fa fa-search"></i>--}}
                {{--                            <span class="title">Tra cứu vi bằng</span>--}}
                {{--                        </p>--}}
                {{--                    </a>--}}
                {{--                </li>--}}
                @if($vp !== "2190")

                <li {!! (Request::is('admin/suutra/create') ? 'class="active"' : '') !!} @if($role=="ke-toan") hidden @endif>
                    <a href="{{ route('createSuutra') }}">
                        <p class="qkpnew">
                            <i class="fa fa-plus"></i>
                            <span class="title">Nhập hồ sơ giao dịch</span>
                        </p>
                    </a>
                </li>
                @endif
								@if($role=="admin" || $vp === "2190")

					    <li {!! (Request::is('admin/suutra/prevent/create') ? 'class="active"' : '') !!} @if($role=="ke-toan") hidden @endif>
                    <a href="{{ route('createSuutraSTP') }}">
                        <p class="qkpnew">
                            <i class="fa fa-plus"></i>
                            <span class="title">Nhập hồ sơ ngăn chặn</span>
                        </p>
                    </a>
                </li>
								@endif

                <li {!! (Request::is('admin/report/*') ? 'class="active"' : '') !!}>

                    <a href="{{ route('indexReport') }}" onclick="loadingLeftMenu()">
                        <p class="qkpnew">
                            <i class="fa fa-area-chart"></i>
                            Thống kê
                        </p>
                    </a>
                </li>
                @if($vp !== "2190")
                <li {!! (Request::is('admin/bds/*') ? 'class="active"' : '') !!}>
                    <a href="{{ route('indexBds') }}">
                            <p class="qkpnew">
                                <i class="fa fa-file"></i>
                                <span class="title">Quản lý file BDS</span>
                            </p>
                        </a>
                    </li>
                @endif
				 <li {!! (Request::is('admin/lich-su-chinh-sua') ? 'class="active"' : '') !!} >
                    <a href="#">
                        <p class="qkpnew">
                            <i class="fa fa-key"></i>
                            <span class="title">Lịch sử</span>
                            <span class="fa fa-angle-double-down" style="font-size: 16px"></span>
                        </p>
                    </a>
                    <ul class="nav child_menu">
                        @if($vp !== "2190")
                        <li {!! (Request::is('admin/lich-su-chinh-suu-duong-su/*') ? 'class="active"' : '') !!}>
                            <a href="{{route('indexKhachHangLog')}}">
                                 
								 <span class="title">Chỉnh sửa Đương sự</span>
                            </a>
                        </li>
                        <li {!! (Request::is('admin/lich-su-chinh-suu-tai-san/*') ? 'class="active"' : '') !!}>
                            <a href="{{route('indexTaiSanLog')}}">
                                 
								 <span class="title">Chỉnh sửa tài sản</span>
                            </a>
                        </li>
						 <li {!! (Request::is('admin/suutra-log/*') ? 'class="active"' : '') !!}>
                            <a href="{{route('suutralogIndex')}}">
                                
								<span class="title">Chỉnh sửa giao dịch</span>
                            </a>
                        </li>
                        @endif
                        @if($role=='admin'||$role=='chuyen-vien-so' || $role == 'truong-van-phong')

					<li {!! (Request::is('admin/history-search/*') ? 'class="active"' : '') !!}>
                        <a href="{{ route('historySearch') }}">
                            <p class="qkpnew">
                                <i class="fa fa-history"></i>
						<span class="title">Lịch sử tìm kiếm</span>

                            </p>
                        </a>
                    </li>
                    @endif
					

                    </ul>
                </li>
                     @if($role=='admin'||$role=='chuyen-vien-so')
                    <li {!! (Request::is('admin/office/*') ? 'class="active"' : '') !!}>
                        <a href="{{ route('indexChiNhanh') }}">
                            <p class="qkpnew">
                                <i class="fa fa-hospital-o"></i>
                                Quản lý văn phòng
                            </p>
                        </a>
                    </li>
                @endif
                @if($role=='truong-van-phong')
                    <li {!! (Request::is('admin/office/*') ? 'class="active"' : '') !!}>
                        <a href="{{ route('editChiNhanh',\App\Models\NhanVienModel::find(\Sentinel::check()->id)->nv_vanphong) }}">
                            <p class="qkpnew">
                                <i class="fa fa-hospital-o"></i>
                                Quản lý văn phòng
                            </p>
                        </a>
                    </li>
                @endif
                @if($role=='admin'||$role=='truong-van-phong')
                    <li {!! (Request::is('admin/employee/*') ? 'class="active"' : '') !!}>
                        <a href="{{ route('indexNhanVien') }}">
                            <p class="qkpnew"><i class="fa fa-users"></i>
                                Quản lý nhân viên
                            </p>
                        </a>
                    </li>

                @endif
                {{-- @if($role=='truong-van-phong'||$role=='admin' || $role=='chuyen-vien-so')
                    <li class="{{ Request::is('admin/kieuhopdongs/*') ? 'active' : '' }}">
                        <a href="{!! route('admin.kieuhopdongs.index') !!}">
                            <p class="qkpnew">
                                <i class="fa fa-credit-card"></i>
                                Kiểu Hợp Đồng
                            </p>
                        </a>
                    </li>
                
                @endif --}}
                @if($vp !== "2190")

                <li {!! (Request::is('admin/van-ban/*') ? 'class="active"' : '') !!}>
                        <a href="{{  route('indexVB') }}">
                            <p class="qkpnew">
                                <i class="fa fa-file-text-o"></i>
                                Quản lý Văn bản
                            </p>
                        </a>
                    </li>
                    @endif
                @if($role=='admin'||$role=='chuyen-vien-so')
                @if($vp !== "2190")

                    <li>
                        <a href="{{ url(route('adminIndex')) }}">
                            <p class="qkpnew">
                                <i class="fa fa-bell"></i>
                                <span class="title">Quản lý thông báo</span>
                            </p>
                        </a>
                    </li>
                @endif
                @endif
				  <!-- <li>
                        <a href="{{ url(route('historyLogin')) }}">
                            <p class="qkpnew">
                                <i class="fa fa-bell"></i>
                                <span class="title">Quản lý đăng nhập</span>
                            </p>
                        </a>
                    </li> -->
                @if(\App\Models\RoleUsersModel::where('user_id',Sentinel::getUser()->id)->first()->role_id==10)
                @if($vp !== "2190")

                    <li {!! (Request::is('admin/roles') || Request::is('admin/permissions') || Request::is('admin/manager/*') ? 'class="active"' : '') !!} >
                        <a href="{{route('admin.manager.users.index')}}">
                            <p class="qkpnew">
                                <i class="fa fa-key"></i>
                                <span class="title">Quản trị tài khoản</span>
                                {{--                                <span class="fa fa-angle-double-down" style="font-size: 16px"></span>--}}
                            </p>
                        </a>
                        {{--                        <ul class="nav child_menu">--}}
                        {{--                            <li {!! (Request::is('admin/manager/users') ? 'class="active"' : '') !!}>--}}
                        {{--                                <a href="{{route('admin.manager.users.index')}}">--}}
                        {{--                                    Người dùng--}}
                        {{--                                </a>--}}
                        {{--                            </li>--}}
                        {{--                        </ul>--}}
                    </li>
                @endif
                @endif
                {{--                <li {!! (Request::is('admin/suutra/cong-van-ngan-chan/*') ? 'class="active"' : '') !!}>--}}
                {{--                    <a href="{{ route('createCongVanNganChan') }}">--}}
                {{--                        <p class="qkpnew">--}}
                {{--                            <i class="fa fa-font"></i>--}}
                {{--                            <span class="title">Công văn ngăn chặn</span>--}}
                {{--                        </p>--}}
                {{--                    </a>--}}
                {{--                </li>--}}
               
			 {{-- @if($role=='admin'||$role=='chuyen-vien-so'||$role='truong-van-phong')
				                 <li {!! (Request::is('admin/templates') ? 'class="active"' : '') !!} >
							   <a href="#">
                        <p class="qkpnew">
                            <i class="fa fa-font"></i>
                            <span class="title">Mẫu</span>
                            <span class="fa fa-angle-double-down" style="font-size: 16px"></span>
                        </p>
                    </a>
					<ul class="nav child_menu">
					<li {!! (Request::is('admin/templates/loai-hop-dong/*') ? 'class="active"' : '') !!}>
                            <a href="{{ route('admin.templates.loai-hop-dong.index') }}">

                                <p class="qkpnew">
                                    <i class="fa fa-font"></i>
                                    <span class="title">Mẫu nhập liệu hợp đồng</span>
                                </p>
                            </a>
                        </li>
                        <li {!! (Request::is('admin/templates/tai-san/*') ? 'class="active"' : '') !!}>
                            <a href="{{ route('admin.templates.tai-san.index') }}">
                                <p class="qkpnew">
                                    <i class="fa fa-font"></i>
                                    <span class="title">Mẫu nhập liệu tài sản</span>
                                </p>
                            </a>
                        </li>
                        <li {!! (Request::is('admin/templates/loai-khach-hàng/*') ? 'class="active"' : '') !!}>
                            <a href="{{ route('admin.templates.loai-khach-hang.index') }}">
                                <p class="qkpnew">
                                    <i class="fa fa-font"></i>
                                    <span class="title">Mẫu nhập liệu đương sự</span>
                                </p>
                            </a>
                        </li>
					</ul>
							 
</li>

                        
                        @endif --}}

                @if($role=='admin')
                @if($vp !== "2190")

                <li {!! (Request::is('admin/bank/*') ? 'class="active"' : '') !!}>
                    <a href="{{ route('indexBank') }}">
                            <p class="qkpnew">
                                <i class="fa fa-money"></i>
                                <span class="title">Ngân hàng</span>
                            </p>
                        </a>
                    </li>
                    <li {!! (Request::is('admin/suutr/*') ? 'class="active"' : '') !!}>
                        <a href="{{ route('viewsolr') }}">
                                    <p class="qkpnew">
                                    <i class="fa fa-font"></i>
                                        <span class="title">Cập nhật tra cứu</span>
                                    </p>
                                </a>
                        </li>
                @endif
                @endif

    

                @if($role=='admin'||$role=='chuyen-vien-so')
{{--                    <li {!! (Request::is('admin/templates/tai-san/*') ? 'class="active"' : '') !!}>--}}
{{--                        <a href="{{ route('admin.templates.tai-san.index') }}">--}}
{{--                            <p class="qkpnew">--}}
{{--                                <i class="fa fa-font"></i>--}}
{{--                                <span class="title">Mẫu nhập liệu tài sản</span>--}}
{{--                            </p>--}}
{{--                        </a>--}}
{{--                    </li>--}}
{{--                    <li {!! (Request::is('admin/templates/loai-khach-hàng/*') ? 'class="active"' : '') !!}>--}}
{{--                        <a href="{{ route('admin.templates.loai-khach-hang.index') }}">--}}
{{--                            <p class="qkpnew">--}}
{{--                                <i class="fa fa-font"></i>--}}
{{--                                <span class="title">Mẫu nhập liệu đương sự</span>--}}
{{--                            </p>--}}
{{--                        </a>--}}
{{--                    </li>--}}

                @endif
            </ul>
        </div>
    </div>
</div>
