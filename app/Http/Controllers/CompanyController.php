<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\CompanyRepository as Company;
use App\Repositories\UserCompanyRepository as UserCompany;


//Mailer....
use Illuminate\Support\Facades\Mail;
use App\Mail\CompanySignup;

class CompanyController extends Controller
{
    protected $company;
    protected $userCompany;


    public function __construct(Company $company, UserCompany $userCompany){
        $this->company = $company;
        $this->userCompany = $userCompany;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->get('data');

        //validate....
        $rules = $this->company->create_rules;
        $validator = $this->validate($request, $rules);

        if(!empty($validator)){
            return response()->json([
                'success' => false,
                'data' => $validator
            ], 400);
        }


        //If we pass validation lets create company and output success :)
        $company = $this->company->create($data);

        //Now lets attach the company to the user
        $user_company = $this->userCompany->attach($this->userId(), $company->id);

        Mail::to($company->company_email)->send(new CompanySignup($company));

        return response()->json([
            'success' => true,
            'data' => $company
        ], 201);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $company = $this->company->find($id);
        return response()->json([
            'success' => true,
            'data' => $company
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $data = $request->get('data');

        //validate....
        $rules = $this->company->update_rules;
        $validator = $this->validate($request, $rules);

        if(!empty($validator)){
            return response()->json([
                'success' => false,
                'data' => $validator
            ], 400);
        }

        //If we pass validation lets update company and output success :)
        $company = $this->company->update($this->userId(), $data);

        return response()->json([
            'success' => true,
            'data' => $company
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $company = $this->company->delete($id);
        return response()->json([
            'success' => true
        ], 200);
    }

}
