<div id="sidebarMain" class="d-none">
    <aside class="js-navbar-vertical-aside navbar navbar-vertical-aside navbar-vertical navbar-vertical-fixed navbar-expand-xl navbar-bordered  ">
        <div class="navbar-vertical-container text-capitalize">
            <div class="navbar-vertical-footer-offset">
                <div class="navbar-brand-wrapper justify-content-between">
                    <!-- Logo -->
                    @php($roles=\App\Model\EmployeeRole::where('admin_id', auth('admin')->user()->id)->first())
                    @php($admin=\App\Model\Admin::where('id', auth('admin')->user()->id)->first())
                    @php($store_logo=\App\Model\BusinessSetting::where(['key'=>'logo'])->first()->value)
                    <a class="navbar-brand" aria-label="Front">
                        <img class="navbar-brand-logo" onerror="this.src='{{asset('public/assets/admin/img/160x160/img2.jpg')}}'" src="{{asset('storage/app/public/store/'.$store_logo)}}" alt="Logo">
                        <img class="navbar-brand-logo-mini" onerror="this.src='{{asset('public/assets/admin/img/160x160/img2.jpg')}}'" src="{{asset('storage/app/public/store/'.$store_logo)}}" alt="Logo">
                    </a>
                    <!-- End Logo -->

                    <!-- Navbar Vertical Toggle -->
                    <button type="button" class="js-navbar-vertical-aside-toggle-invoker navbar-vertical-aside-toggle btn btn-icon btn-xs btn-ghost-dark">
                        <i class="tio-clear tio-lg"></i>
                    </button>
                    <!-- End Navbar Vertical Toggle -->
                </div>

                <!-- Content -->
                <div class="navbar-vertical-content">
                    <ul class="navbar-nav navbar-nav-lg nav-tabs">
                        <!-- Dashboards -->
                        @if($roles->orders == 1)
                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin')?'show':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('admin.dashboard')}}" title="Dashboards">
                                <i class="tio-home-vs-1-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{trans('messages.dashboard')}}
                                </span>
                            </a>
                        </li>
                        @endif
                        <!-- End Dashboards -->

                        @if($roles->orders == 1)
                        <li class="nav-item">
                            <small class="nav-subtitle">{{trans('messages.order')}} {{trans('messages.section')}}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        <!-- Pages -->
                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/orders*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-shopping-cart nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{trans('messages.order')}}
                                </span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: {{Request::is('admin/order*')?'block':'none'}}">
                                @if($admin->role == 'admin')
                                <li class="nav-item {{Request::is('admin/orders/list/all')?'active':''}}">
                                    <a class="nav-link" href="{{route('admin.orders.list',['all'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{trans('messages.all')}}
                                            <span class="badge badge-info badge-pill ml-1">
                                                {{\App\Model\Order::count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                @endif
                                <li class="nav-item {{Request::is('admin/orders/list/pending')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.orders.list',['pending'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{trans('messages.pending')}}
                                            <span class="badge badge-soft-info badge-pill ml-1">
                                                {{\App\Model\Order::where(['order_status'=>'pending'])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/orders/list/confirmed')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.orders.list',['confirmed'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{trans('messages.confirmed')}}
                                            <span class="badge badge-soft-success badge-pill ml-1">
                                                {{\App\Model\Order::where(['order_status'=>'confirmed'])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/orders/list/processing')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.orders.list',['processing'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{trans('messages.processing')}}
                                            <span class="badge badge-warning badge-pill ml-1">
                                                {{\App\Model\Order::where(['order_status'=>'processing'])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/orders/list/out_for_delivery')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.orders.list',['out_for_delivery'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{trans('messages.out_for_delivery')}}
                                            <span class="badge badge-warning badge-pill ml-1">
                                                {{\App\Model\Order::where(['order_status'=>'out_for_delivery'])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>

                                @if($admin->role == 'admin')
                                <li class="nav-item {{Request::is('admin/orders/list/delivered')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.orders.list',['delivered'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{trans('messages.delivered')}}
                                            <span class="badge badge-success badge-pill ml-1">
                                                {{\App\Model\Order::where(['order_status'=>'delivered'])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/orders/list/returned')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.orders.list',['returned'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{trans('messages.returned')}}
                                            <span class="badge badge-soft-danger badge-pill ml-1">
                                                {{\App\Model\Order::where(['order_status'=>'returned'])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/orders/list/failed')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.orders.list',['failed'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{trans('messages.failed')}}
                                            <span class="badge badge-danger badge-pill ml-1">
                                                {{\App\Model\Order::where(['order_status'=>'failed'])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>

                                <li class="nav-item {{Request::is('admin/orders/list/canceled')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.orders.list',['canceled'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{trans('messages.canceled')}}
                                            <span class="badge badge-soft-dark badge-pill ml-1">
                                                {{\App\Model\Order::where(['order_status'=>'canceled'])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </li>
                        <!-- End Pages -->
                        @endif



                        @if($roles->products == 1)

                        <li class="nav-item">
                            <small class="nav-subtitle">{{trans('messages.product')}} {{trans('messages.section')}}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        <!-- Pages -->
                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/banner*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-image nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{trans('messages.banner')}}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: {{Request::is('admin/banner*')?'block':'none'}}">
                                <li class="nav-item {{Request::is('admin/banner/add-new')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.banner.add-new')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{trans('messages.add')}} {{trans('messages.new')}}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/banner/list')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.banner.list')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{trans('messages.list')}}</span>
                                    </a>
                                </li>


                            </ul>
                        </li>
                        <!-- End Pages -->



                        <!-- SERVICes -->
                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/service*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-category nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Service</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: {{Request::is('admin/service*')?'block':'none'}}">
                                <li class="nav-item {{Request::is('admin/service/add')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.service.add')}}" title="add new service">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">Services</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <!-- End SERVICW -->


                        <!-- Pages -->
                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/category*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-category nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{trans('messages.category')}}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: {{Request::is('admin/category*')?'block':'none'}}">
                                <li class="nav-item {{Request::is('admin/category/add')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.category.add')}}" title="add new category">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{trans('messages.category')}}</span>
                                    </a>
                                </li>

                                <li class="nav-item {{Request::is('admin/category/add-sub-category')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.category.add-sub-category')}}" title="add new sub category">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{trans('messages.sub_category')}}</span>
                                    </a>
                                </li>

                                {{--<li class="nav-item {{Request::is('admin/category/add-sub-sub-category')?'active':''}}">
                                <a class="nav-link " href="{{route('admin.category.add-sub-sub-category')}}" title="add new sub sub category">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">Sub-Sub-Category</span>
                                </a>
                        </li>--}}
                    </ul>
                    </li>
                    <!-- End Pages -->


                    <!-- Pages -->
                    <li class="navbar-vertical-aside-has-menu {{Request::is('admin/attribute*')?'active':''}}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('admin.attribute.add-new')}}">
                            <i class="tio-apps nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                {{trans('messages.attribute')}}
                            </span>
                        </a>
                    </li>
                    <!-- End Pages -->

                    <!-- Pages -->
                    <li class="navbar-vertical-aside-has-menu {{Request::is('admin/offers*')?'active':''}}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('admin.offers.add-new')}}">
                            <i class="fas fa-gift nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                Offers
                            </span>
                        </a>
                    </li>
                    <!-- End Pages -->

                    @endif
                    <!-- Pages -->

                    <!-- End Pages -->
                    @if($roles->products == 1)
                    <!-- Pages -->
                    <li class="navbar-vertical-aside-has-menu {{Request::is('admin/product*')?'active':''}}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                            <i class="tio-premium-outlined nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{trans('messages.product')}}</span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: {{Request::is('admin/product*')?'block':'none'}}">
                            <li class="nav-item {{Request::is('admin/product/add-new')?'active':''}}">
                                <a class="nav-link " href="{{route('admin.product.add-new')}}" title="add new product">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{trans('messages.add')}} {{trans('messages.new')}}</span>
                                </a>
                            </li>
                            <li class="nav-item {{Request::is('admin/product/list')?'active':''}}">
                                <a class="nav-link " href="{{route('admin.product.list')}}" title="product list">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{trans('messages.list')}}</span>
                                </a>
                            </li>

                        </ul>
                    </li>
                    <!-- End Pages -->
                    @endif

                    @if($roles->business_section == 1)


                    <li class="nav-item">
                        <small class="nav-subtitle" title="Layouts">{{trans('messages.business')}} {{trans('messages.section')}}</small>
                        <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                    </li>

                    <li class="nav-item {{Request::is('admin/business-settings/store-setup')?'active':''}}">
                        <a class="nav-link " href="{{route('admin.business-settings.store-setup')}}">
                            <span class="tio-circle nav-indicator-icon"></span>
                            <span class="text-truncate">Settings</span>
                        </a>
                    </li>

                    <!-- Pages -->
                    <li class="navbar-vertical-aside-has-menu {{Request::is('admin/message*')?'active':''}}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('admin.message.list')}}">
                            <i class="tio-messages nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                {{trans('messages.messages')}}
                            </span>
                        </a>
                    </li>
                    <!-- End Pages -->



                    <!-- Pages -->
                    <!-- <li class="navbar-vertical-aside-has-menu {{Request::is('admin/reviews*')?'active':''}}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('admin.reviews.list')}}">
                            <i class="tio-star nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                {{trans('messages.product')}} {{trans('messages.reviews')}}
                            </span>
                        </a>
                    </li> -->
                    <!-- End Pages -->


                    <!-- Notifications -->
                    <li class="navbar-vertical-aside-has-menu {{Request::is('admin/notification*')?'active':''}}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('admin.notification.add-new')}}">
                            <i class="tio-notifications nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                {{trans('messages.send')}} {{trans('messages.notification')}}
                            </span>
                        </a>
                    </li>

                    <!-- End Pages -->

                    <!-- Pages -->
                    <li class="navbar-vertical-aside-has-menu {{Request::is('admin/coupon*')?'active':''}}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('admin.coupon.add-new')}}">
                            <i class="tio-gift nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{trans('messages.coupon')}}</span>
                        </a>
                    </li>
                    <!-- End Pages -->

                    <!-- Pages -->
                    <li class="navbar-vertical-aside-has-menu {{Request::is('admin/coupon*')?'active':''}}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('admin.coupon.custom.edit')}}">
                            <i class="tio-gift nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Custom Coupon</span>
                        </a>
                    </li>
                    <!-- End Pages -->
                    @endif

                    <!-- Start Zones -->
                    <li class="nav-item">
                        <small class="nav-subtitle" title="Documentation">Zones Section</small>
                        <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                    </li>

                    <li class="navbar-vertical-aside-has-menu {{Request::is('admin/zone/home*')?'active':''}}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('admin.zone.home')}}">
                            <i class="tio-poi-user nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                Add Zone
                            </span>
                        </a>
                    </li>

                    <li class="navbar-vertical-aside-has-menu {{Request::is('admin/zone/list*')?'active':''}}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('admin.zone.list')}}">
                            <i class="tio-poi-user nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                Zones List
                            </span>
                        </a>
                    </li>
                    <!-- End Zones -->

                    <li class="nav-item">
                        <small class="nav-subtitle" title="Documentation">{{trans('messages.customer')}} {{trans('messages.section')}}</small>
                        <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                    </li>

                    <li class="navbar-vertical-aside-has-menu {{Request::is('admin/customer*')?'active':''}}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('admin.customer.list')}}">
                            <i class="tio-poi-user nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                {{trans('messages.customer')}} {{trans('messages.list')}}
                            </span>
                        </a>
                    </li>
                    <!-- End Pages -->



                    <!-- START BRANCH Pages -->
                    <li class="nav-item">
                        <small class="nav-subtitle" title="Documentation">Branch section</small>
                        <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                    </li>

                    <li class="navbar-vertical-aside-has-menu {{Request::is('admin/branch*')?'active':''}}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('admin.branch.list')}}">
                            <i class="tio-poi-user nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                Branch list
                            </span>
                        </a>
                    </li>
                    <!-- End BRANCH Pages -->




                    <li class="nav-item">
                        <div class="nav-divider"></div>
                    </li>

                    <li class="nav-item">
                        <small class="nav-subtitle" title="Documentation">{{trans('messages.report_and_analytics')}}</small>
                        <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                    </li>

                    <!-- Pages -->
                    <li class="navbar-vertical-aside-has-menu {{Request::is('admin/report*')?'active':''}}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                            <i class="tio-report-outlined nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{trans('messages.reports')}}</span>
                        </a>

                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: {{Request::is('admin/report*')?'block':'none'}}">

                            <li class="nav-item {{Request::is('admin/report/earning')?'active':''}}">
                                <a class="nav-link " href="{{route('admin.report.earning')}}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{trans('messages.earning')}} {{trans('messages.report')}}</span>
                                </a>
                            </li>
                            <li class="nav-item {{Request::is('admin/report/order')?'active':''}}">
                                <a class="nav-link " href="{{route('admin.report.order')}}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{trans('messages.order')}} {{trans('messages.report')}}</span>
                                </a>
                            </li>

                        </ul>
                    </li>
                    <!-- End Pages -->


                    <li class="nav-item" style="padding-top: 100px">
                        <div class="nav-divider"></div>
                    </li>
                    </ul>
                </div>
                <!-- End Content -->
            </div>
        </div>
    </aside>
</div>

<div id="sidebarCompact" class="d-none">

</div>



{{--<script>
    $(document).ready(function () {
        $('.navbar-vertical-content').animate({
            scrollTop: $('#scroll-here').offset().top
        }, 'slow');
    });
</script>--}}