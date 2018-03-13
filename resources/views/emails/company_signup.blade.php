@extends('layouts.layout')
@section('content')
    <div class="content">
        <table>
            <tr>
                <td>
                    <h3 style="color: #41a2d5; font-weight: 500;">Thank you for registering {{ $company->company_name }} with the Social Media Income platform!</h3>
                    <p class="lead">We are very excited to have you on board!</p>

                    <p>Our mission is to provide you an excellent experience and help with your marketing efforts.</p>
                    <p>By using the Social Media Income Platform, you will be able to spread the word about your product or service in a very powerful and trusted way.</p>

                    <p>The idea behind using the SMI platform is that each person filling the order will have to convince their very own private network of trusted friends and/or family members to use your product or service.</p>

                    <p>We encourage you to provide an engagement bonus to the SMI platform users which will fill your order.</p>
                    <p>An engagement bonus is basically an extra commission for the person filling your order in the event that they assist you in making a sale.</p>
                    <p>By providing an engagement bonus, this will cause the person filling your order to be incentivised and they will work a little harder to convince a trusted friend or family member to purchase your product or service.</p>

                    <p>&nbsp;</p>
                    <p>&nbsp;</p>
                    {{--<p style="background: #262f3e; border: 1px solid #293444; padding: 20px;">--}}
                    {{--Phasellus dictum sapien a neque luctus cursus. Pellentesque sem dolor, fringilla et pharetra vitae. <a href="#" style="color: #F8F8F8; text-decoration: underline; font-weight: 400;">Click it! &raquo;</a>--}}
                    {{--</p>--}}

                    <p>We really appreciate your interest in working with the SMI platform and will do our best to make it worth your while!</p>
                    <p>&nbsp;</p>
                    <p>&nbsp;</p>
                    <p>Sincerely, <br> Social Media Income Team</p>

                    <table class="social" style="border-top: 1px solid rgba(120, 130, 140, 0.13); margin-top: 40px;" width="100%">
                        <tr>
                            <td>

                                <table align="left" class="column">
                                    <tr>
                                        <td>
                                            <h5 class="">Connect with Us:</h5>
                                            <p class="">
                                                <a href="https://www.facebook.com/smicoin" class="soc-btn fb"><img src="https://s3.amazonaws.com/socialmediaincome.com/images/fb-icon.png" alt="Social Media Income Facebook" width="50"></a>
                                                <a href="https://twitter.com/smicoin" class="soc-btn tw"><img src="https://s3.amazonaws.com/socialmediaincome.com/images/twitter-icon.png" alt="Social Media Income Twitter" width="50"></a>
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