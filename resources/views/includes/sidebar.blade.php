<div class="left side-menu">
    <div class="sidebar-inner slimscrollleft">



        <div id="sidebar-menu">
            <ul>
                @if(\Illuminate\Support\Facades\Auth::user()->user_type==0)


                {{--<li>--}}
                    {{--<a href="/admin/home" class="waves-effect"><i class="mdi mdi-home"></i><span> Dashboard </span></a>--}}
                {{--</li>--}}
                    <li>
                        <a href="/admin/rules" class="waves-effect"><i class="mdi mdi-format-list-bulleted"></i><span> Rules </span></a>
                    </li>

                    <li>
                        <a href="/admin/rules-added-by-admin" class="waves-effect"><i class="mdi mdi-format-list-bulleted"></i><span> All Rules  </span></a>
                    </li>


                <li>
                        <a href="/admin/merchants" class="waves-effect"><i class="mdi mdi-nature-people "></i><span> Merchants </span></a>
                </li>

                    <li>
                        <a href="/admin/api-usage" class="waves-effect"><i class="mdi mdi-chart-histogram "></i><span> API Usage </span></a>
                    </li>

                    <li>
                        <a href="/admin/api-requests" class="waves-effect"><i class="mdi mdi-database "></i><span> API Requests </span></a>
                    </li>

                    <li>
                        <a href="/admin/api-performance" class="waves-effect"><i class="mdi mdi-chart-line "></i><span> API Performance </span></a>
                    </li>

                    <li>
                        <a href="/admin/api-explorer" class="waves-effect"><i class="mdi mdi-chart-pie "></i><span> API Explorer</span></a>
                    </li>

                    <li>
                        <a href="/admin/business-intelligence" class="waves-effect"><i class="mdi mdi-speedometer "></i><span> Business Intel </span></a>
                    </li>




                <li>
                    <a href="/admin/users" class="waves-effect"><i class="mdi mdi-human-male-female "></i><span> Users </span></a>
                </li>

                <li>
                    <a href="/admin/payments" class="waves-effect"><i class="mdi mdi-credit-card"></i><span> Payments </span></a>
                </li>

                {{--<li>--}}
                    {{--<a href="/admin/orders" class="waves-effect"><i class="mdi mdi-ticket"></i><span> Orders </span></a>--}}
                {{--</li>--}}

                    <li>
                        <a href="/admin/agreements" class="waves-effect"><i class="mdi mdi-ticket"></i><span> Agreements </span></a>
                    </li>

                    <li>
                        <a href="/admin/subscriptions" class="waves-effect"><i class="mdi mdi-credit-card-multiple"></i><span>Subscriptions</span></a>
                    </li>

                    <li>
                        <a href="/admin/bizpay-subscriptions" class="waves-effect"><i class="mdi mdi-credit-card-multiple"></i><span>Bizpay Subscriptions</span></a>
                    </li>

                    <li>
                        <a href="/admin/settings" class="waves-effect"><i class="mdi mdi-settings"></i><span> Settings </span></a>
                    </li>



                    <li>
                        <a href="/admin/profile" class="waves-effect"><i class="mdi mdi-human"></i><span>  Profile </span></a>
                    </li>

                    <li>
                        <a href="/admin/change-password" class="waves-effect"><i class="mdi mdi-lock"></i><span>  Change Password </span></a>
                    </li>

                @endif

                @if(\Illuminate\Support\Facades\Auth::user()->user_type==1)

                        <li>
                            <a href="/merchant-admin/settings" class="waves-effect"><i class="mdi mdi-settings"></i><span> Settings </span></a>
                        </li>

                        <li>
                            <a href="/merchant-admin/profile" class="waves-effect"><i class="mdi mdi-human"></i><span>  API & Portal  </span></a>
                        </li>


                        <li>
                            <a href="/merchant-admin/rules" class="waves-effect"><i class="mdi mdi-format-list-bulleted"></i><span> Rules </span></a>
                        </li>

                        <li>
                            <a href="/merchant-admin/products" class="waves-effect"><i class="mdi mdi-presentation"></i><span> Products </span></a>
                        </li>

                        <li>
                            <a href="/merchant-admin/managers" class="waves-effect"><i class="mdi mdi-nature-people"></i><span> Managers </span></a>
                        </li>

                <li>
                    <a href="/merchant-admin/clients" class="waves-effect"><i class="mdi mdi-human-male-female"></i><span> Clients </span></a>
                </li>

                        {{--<li>--}}
                            {{--<a href="/merchant-admin/orders" class="waves-effect"><i class="mdi mdi-credit-card-multiple"></i><span> Client Orders </span></a>--}}
                        {{--</li>--}}

                  <li>
                            <a href="/merchant-admin/payments" class="waves-effect"><i class="mdi mdi-credit-card"></i><span> Client Payments </span></a>
                        </li>

                  <li>
                            <a href="/merchant-admin/subscriptions" class="waves-effect"><i class="mdi mdi-key-plus"></i><span> Client Subscriptions </span></a>
                        </li>



                        <li>
                            <a href="/merchant-admin/bizpay-subscription" class="waves-effect"><i class="mdi mdi-key-plus"></i><span> Bizpay Subscription </span></a>
                        </li>



                        <li>
                            <a href="/merchant-admin/change-password" class="waves-effect"><i class="mdi mdi-lock"></i><span>  Change Password </span></a>
                        </li>


                    @endif


                    @if(\Illuminate\Support\Facades\Auth::user()->user_type==2)

                        <li>
                            <a href="/merchant-manager/clients" class="waves-effect"><i class="mdi mdi-human-male-female"></i><span> Clients </span></a>
                        </li>

                        <li>
                            <a href="/merchant-manager/products" class="waves-effect"><i class="mdi mdi-presentation"></i><span> Products </span></a>
                        </li>

                        {{--<li>--}}
                            {{--<a href="/merchant-admin/orders" class="waves-effect"><i class="mdi mdi-credit-card-multiple"></i><span> Client Orders </span></a>--}}
                        {{--</li>--}}


                        <li>
                            <a href="/merchant-manager/payments" class="waves-effect"><i class="mdi mdi-credit-card"></i><span> Client Payments </span></a>
                        </li>

                        <li>
                            <a href="/merchant-manager/subscriptions" class="waves-effect"><i class="mdi mdi-key-plus"></i><span> Client Subscriptions </span></a>
                        </li>

                        <li>
                            <a href="/merchant-manager/profile" class="waves-effect"><i class="mdi mdi-human"></i><span> Profile </span></a>
                        </li>

                        <li>
                            <a href="/merchant-manager/change-password" class="waves-effect"><i class="mdi mdi-lock"></i><span>  Change Password </span></a>
                        </li>


                        @endif

                    @if(\Illuminate\Support\Facades\Auth::user()->user_type==3)

                        <li>
                            <a href="/client/shop" class="waves-effect"><i class="mdi mdi-shopping"></i><span>  Shop </span></a>
                        </li>

                        <li>
                            <a href="/client/payments" class="waves-effect"><i class="mdi mdi-credit-card"></i><span>  Payments </span></a>
                        </li>

                        <li>
                            <a href="/client/subscriptions" class="waves-effect"><i class="mdi mdi-key-plus"></i><span>  Subscriptions </span></a>
                        </li>


                        {{--<li>--}}
                            {{--<a href="/client/orders" class="waves-effect"><i class="mdi mdi-format-list-bulleted"></i><span>  Orders </span></a>--}}
                        {{--</li>--}}

                        <li>
                            <a href="/client/profile" class="waves-effect"><i class="mdi mdi-human"></i><span>  Profile </span></a>
                        </li>

                        <li>
                            <a href="/client/change-password" class="waves-effect"><i class="mdi mdi-lock"></i><span>  Change Password </span></a>
                        </li>
                        {{--<li>--}}
                            {{--<a href="/client/cart" class="waves-effect"><i class="mdi mdi-cart"></i><span>  Cart </span></a>--}}
                        {{--</li>--}}


                    @endif




            </ul>
        </div>
        <div class="clearfix"></div>
    </div> <!-- end sidebarinner -->
</div>