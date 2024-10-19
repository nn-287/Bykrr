<div style="background-image: url('{{ asset('storage/landing-image.jpg') }}'); background-size: cover; background-attachment: fixed; height: 100vh; display: flex; align-items: center; justify-content: center;">
   

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="Bykr">
  <meta name="author" content="Bykr">
      <!-- CSRF Token -->
	  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>Bykr</title>

 <!-- Bootstrap css -->

 <link rel="stylesheet" href="{{asset('assets/customer')}}/css/bootstrap.css?v=2.0"> 
 <!-- <link href="css/bootstrap.css?v=2.0" rel="stylesheet" type="text/css" /> -->
 <link href="{{asset('assets/customer')}}/css/bootstrap.css?v=2.0" rel="stylesheet" type="text/css" />

<!-- Custom css -->
<link href="{{asset('assets/customer')}}/css/ui.css?v=2.0" rel="stylesheet" type="text/css" />
<!-- <link href="css/ui.css?v=2.0" rel="stylesheet" type="text/css" /> -->

<link href="{{asset('assets/customer')}}/css/responsive.css?v=2.0" rel="stylesheet" type="text/css" />

<!-- Font awesome 5 -->
<link href="{{asset('assets/customer')}}/fonts/fontawesome/css/all.min.css" type="text/css" rel="stylesheet">
<!-- <link href="fonts/fontawesome/css/all.min.css" type="text/css" rel="stylesheet"> -->

</head>



<div class="container"> 
    <div class="column">
    @php($store_logo=\App\Model\BusinessSetting::where(['key'=>'logo'])->first()->value)
				
         <div><img class="logo" height="100" src="{{asset('storage/store/'.$store_logo)}}"></div>
        <div class="text-large pb-3"><b>Coming soon!</b></div> 
        
        <div class="text" ><b>Bykr</b> is getting ready to bring you an unforgettable dining experience. <br> From mouthwatering dishes to cozy ambiance, we're cooking up something truly special</div>

    </div>
</div>


<style>
    .content {
    background-color: rgba(255, 255, 255, 0.8); /* Add background color for better readability */
    padding: 20px;
}

.column {
    text-align: center;
}

.text {
    margin-bottom: 20px;
    font-size: 24px;
}


.text-wrapper {
    position: relative;
    display: inline-block; /* Ensures the wrapper fits the content */
}

.text {
    position: relative;
    z-index: 1; /* Ensures the text appears above the overlay */
    color: #fff; /* Set text color */
    font-size: 18px;
}

.text-large {
    position: relative;
    z-index: 1; /* Ensures the text appears above the overlay */
    color: #fff; /* Set text color */
    font-size: 34px;
}

.text-wrapper::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5); /* Adjust transparency here */
    z-index: 0; /* Ensure the overlay is behind the text */
}

.half-screen {
    width: 50%; /* Adjust this value to control the width of the half screen */
    float: left; /* Ensure that the div takes up only half of the screen */
}

</style>

</div>
