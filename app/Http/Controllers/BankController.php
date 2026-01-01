<?php

namespace App\Http\Controllers;
// use App\Http\Requests\NhanVienRequest;
use App\Models\BankModel;
use App\Models\User;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\View\View;
use PHPUnit\Framework\Constraint\Count;
use URL;
use Illuminate\Pagination;
use Illuminate\Http\Request;

class BankController extends Controller
{
    function index(Request $request)
    {
        $search = $request->name;
        if ($search != null) {
            $bank = BankModel::where('name', 'like', '%' . $search . '%')->orderBy('created_at', 'desc')->paginate(10);
        } else {
            $bank = BankModel::orderBy('created_at', 'desc')->paginate(10);
        }
        $count = Count($bank);
        //total
        $tong = count($bank);
        return view('admin.bank.index ', compact('bank', 'count', 'tong', 'search'));
    }
    function show($id)
    {
        $bank = BankModel::find($id);
        return view('admin.bank.detail', compact('bank'));
    }
    //store bank
    function store(Request $request)
    {
        $bank = new BankModel();
        $bank->name = $request->name;
        $bank->order_number = $request->order_number;
        $bank->save();
        $request->session()->flash('success', 'Thêm thành công');
        return redirect('admin/bank/index');
    }
    //edit and update function
    function create()
    {
        return view('admin.bank.create');
    }
    function edit($id)
    {
        $bank = BankModel::find($id);

        return view('admin.bank.edit', compact('bank'));
    }
    function update(Request $request, $id)
    {
        $bank = BankModel::find($id);
        $bank->name = $request->name;
        $bank->order_number = $request->order_number;
        $bank->save();
        //notification
        $request->session()->flash('success', 'Cập nhật thành công');
        return redirect('admin/bank/index');
    }
    function destroy($id, Request $request)
    {
        $bank = BankModel::find($id);
        $bank->delete();
        //notification
        $request->session()->flash('success', 'Xóa thành công');
        return redirect('admin/bank/index');
    }
}