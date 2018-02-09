@extends('layouts.layout')
@section('content')
    <div class="content">
        <table>
            <tr>
                <td>
                    <h3 style="color: #41a2d5; font-weight: 500;">Hello, {{ $user->name }}!</h3>
                    <p class="lead">You are receiving this email because you have requested to reset your password.</p>

                    <p style="background: #262f3e; border: 1px solid #293444; padding: 20px;">
                    In order to reset your password, please <a href="https://socialmediaincome.com/contribute.html?reset={{ $token }}" style="color: #F8F8F8; text-decoration: underline; font-weight: 400;">Click Here! &raquo;</a>
                    </p>


                    <table class="social" style="border-top: 1px solid rgba(120, 130, 140, 0.13); margin-top: 40px;" width="100%">
                        <tr>
                            <td>

                                <table align="left" class="column">
                                    <tr>
                                        <td>
                                            <h5 class="">Connect with Us:</h5>
                                            <p class="">
                                                <a href="#" class="soc-btn fb"><img src="https://s3.amazonaws.com/socialmediaincome.com/images/fb-icon.png" alt="Social Media Income Facebook" width="50"></a>
                                                <a href="#" class="soc-btn tw"><img src="https://s3.amazonaws.com/socialmediaincome.com/images/twitter-icon.png" alt="Social Media Income Twitter" width="50"></a>
                                            </p>
                                        </td>
                                    </tr>
                                </table>

                                <table align="left" class="column">
                                    <tr>
                                        <td>
                                            <h5 class="">Contact Info:</h5>
                                            <p>Support Email:<br> <a  href="mailto:support@socialmediaincome.com" style="color: #F8F8F8; text-decoration: none; font-weight: 300; padding: 10px 0;">support@socialmediaincome.com</a></p>
                                        </td>
                                    </tr>
                                </table>
                                <span class="clear"></span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
@endsection