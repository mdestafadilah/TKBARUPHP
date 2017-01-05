<?php
/**
 * Created by PhpStorm.
 * User: GitzJoey
 * Date: 9/7/2016
 * Time: 12:33 AM
 */

namespace App\Http\Controllers;

use App\Model\BankBCACSVRecord;
use App\Model\BankUpload;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Storage;
use Validator;
use App\Http\Requests;
use Illuminate\Http\Request;

use App\Model\Bank;
use App\Model\Lookup;

class BankController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $bank = Bank::paginate(10);
        return view('bank.index')->with('banks', $bank);
    }

    public function show($id)
    {
        $bank = Bank::find($id);
        return view('bank.show')->with('bank', $bank);
    }

    public function create()
    {
        $statusDDL = Lookup::where('category', '=', 'STATUS')->get()->pluck('description', 'code');

        return view('bank.create', compact('statusDDL'));
    }

    public function store(Request $data)
    {
        $validator = Validator::make($data->all(), [
            'name' => 'required|string|max:255',
            'short_name' => 'required|string|max:255',
            'branch' => 'required|string|max:255',
            'branch_code' => 'required|string|max:255',
            'status' => 'required',
            'remarks' => 'required|string|max:255',

        ]);

        if ($validator->fails()) {
            return redirect(route('db.master.bank.create'))->withInput()->withErrors($validator);
        } else {

            Bank::create([
                'store_id' => Auth::user()->store->id,
                'name' => $data['name'],
                'short_name' => $data['short_name'],
                'branch' => $data['branch'],
                'branch_code' => $data['branch_code'],
                'status' => $data['status'],
                'remarks' => $data['remarks']
            ]);
            return redirect(route('db.master.bank'));
        }
    }

    public function upload()
    {
        $bankDDL = Lookup::where('category', '=', 'BANKUPLOAD')->get()->pluck('description', 'code');
        $bankUploads = BankUpload::all();

        return view('bank.upload', compact('bankDDL', 'bankUploads'));
    }

    public function edit($id)
    {
        $bank = Bank::find($id);

        $statusDDL = Lookup::where('category', '=', 'STATUS')->get()->pluck('description', 'code');

        return view('bank.edit', compact('bank', 'statusDDL'));
    }

    public function update($id, Request $req)
    {
        Bank::find($id)->update($req->all());
        return redirect(route('db.master.bank'));
    }

    public function delete($id)
    {
        Bank::find($id)->delete();
        return redirect(route('db.master.bank'));
    }

    public function storeUpload(Request $data)
    {
        $validator = Validator::make($data->all(), [
            'bank' => 'required|string|max:255',
            'file_path' => 'required|mimes:csv,txt|size:max:999',
        ]);

        if ($data->hasFile('file_path')) {
            $path = Storage::disk('file_upload')->put($data->file('file_path')->getClientOriginalName(), $data->file('file_path'));
            $bankUpload = BankUpload::create(['bank' => $data->input('bank'), 'filename' => $data->file('file_path')->getClientOriginalName()]);

            Excel::load(storage_path('app/file_upload/' . $path), function ($reader) use ($data, $bankUpload){
                if($data->input('bank') == 'BANKUPLOAD.BCA'){
                    Config::set('excel.import.startRow', 5);

                    $dataRows = $reader->get();

                    $dataRows->each(function ($item, $key) use ($bankUpload){
                        //Check if it already reached the footer
                        if($item['tanggal'] == 'Saldo Awal'){
                            return false;
                        }

                        $date = str_replace("'", "", $item['tanggal']);
                        $date = explode('/', $date);
                        $date = Carbon::create(null, $date[1], $date[0]);
                        $branch = str_replace("'", "", $item['cabang']);
                        $remarks = $item['keterangan'];
                        $amount = floatval($item['jumlah']);
                        $db_cr = $item[''];
                        $balance = floatval($item['saldo']);

                        BankBCACSVRecord::create([
                            'date' => $date,
                            'branch' => $branch,
                            'remarks' => $remarks,
                            'amount' => $amount,
                            'db_cr' => $db_cr,
                            'balance' => $balance,
                            'bank_upload_id' => $bankUpload->id
                        ]);
                    });
                }
            });
        }

        $data->session()->flash('success', 'Upload success.');

        return redirect()->action('BankController@upload');
    }
}