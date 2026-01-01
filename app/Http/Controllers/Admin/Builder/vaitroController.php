<?php

namespace App\Http\Controllers\Admin\Builder;

use App\Http\Requests;
use App\Http\Requests\Admin\Builder\CreatevaitroRequest;
use App\Http\Requests\Admin\Builder\UpdatevaitroRequest;
use App\Repositories\Admin\Builder\vaitroRepository;
use App\Http\Controllers\AppBaseController as InfyOmBaseController;
use App\VaiTroModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use App\Models\Admin\Builder\vaitro;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class vaitroController extends InfyOmBaseController
{
    /** @var  vaitroRepository */
    private $vaitroRepository;

    public function __construct(vaitroRepository $vaitroRepo)
    {
        $this->vaitroRepository = $vaitroRepo;
    }

    /**
     * Display a listing of the vaitro.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {

        $this->vaitroRepository->pushCriteria(new RequestCriteria($request));
        $vaitros = $this->vaitroRepository->all();
        return view('admin.vaitros.index')
            ->with('vaitros', $vaitros);
    }

    /**
     * Show the form for creating a new vaitro.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.vaitros.create');
    }

    /**
     * Store a newly created vaitro in storage.
     *
     * @param CreatevaitroRequest $request
     *
     * @return Response
     */
    public function store(CreatevaitroRequest $request)
    {
        $input = $request->all();

        /*        $vaitro = $this->vaitroRepository->create($input);*/

        VaiTroModel::create([
            'vt_nhan'=>$request->vt_nhan
        ]);
        Flash::success('Vai trò đã lưu thành công');

        return redirect(route('admin.vaitros.index'));
    }

    /**
     * Display the specified vaitro.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $vaitro = VaiTroModel::find($id);

        if (empty($vaitro)) {
            Flash::error('vaitro not found');

            return redirect(route('admin.vaitros.index'));
        }

        return view('admin.vaitros.show')->with('vaitro', $vaitro);
    }

    /**
     * Show the form for editing the specified vaitro.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $vaitro = VaiTroModel::find($id);

        if (empty($vaitro)) {
            Flash::error('Không tìm thấy vai trò');

            return redirect(route('admin.vaitros.index'));
        }

        return view('admin.vaitros.edit')->with('vaitro', $vaitro);
    }

    /**
     * Update the specified vaitro in storage.
     *
     * @param  int              $id
     * @param UpdatevaitroRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatevaitroRequest $request)
    {
        $vaitro = VaiTroModel::find($id);


        if (empty($vaitro)) {
            Flash::error('Không tìm thấy vai trò');

            return redirect(route('admin.vaitros.index'));
        }
        $vaitro->update([
'vt_nhan'=>$request->vt_nhan
        ]);


        return redirect(route('admin.vaitros.index'))->with('success', 'Cập nhật vai trò thành công');
    }

    /**
     * Remove the specified vaitro from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
      public function getModalDelete($id = null)
      {
          $error = '';
          $model = '';
          $confirm_route =  route('admin.vaitros.delete',['vt_id'=>$id]);
          return View('admin.layouts/modal_confirmation', compact('error','model', 'confirm_route'));

      }

       public function getDelete($id = null)
       {
           $sample = VaiTroModel::destroy($id);

           // Redirect to the group management page
           return redirect(route('admin.vaitros.index'))->with('success',' Đã xóa thành công');

       }

}
