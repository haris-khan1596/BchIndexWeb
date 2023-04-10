<style>
    #p2p_receipt{
        box-shadow: 0 0 1in -0.25in rgba(0, 0, 0, 0.5);
        padding: 2mm;
        margin: 0 auto;
        width: 80mm;
        background: #FFF;
    }
    ::selection {background: #f31544; color: #FFF;}
    ::moz-selection {background: #f31544; color: #FFF;}
    h1{
        font-size: 1.5em;
        color: #222;
    }
    h2{font-size: .9em;}
    h3{
        font-size: 1.2em;
        font-weight: 300;
        line-height: 2em;
    }
    p{
        font-size: .7em;
        color: #666;
        line-height: 1.2em;
    }

    #top, #mid,#bot{ /* Targets all id with 'col-' */
        border-bottom: 1px solid #EEE;
    }
    #mid{min-height: 80px;}
    #bot{ min-height: 50px;}

    #top .logo{
        height: 60px;
        width: 60px;
        background: url({{ getImage('assets/images/done-512.png') }}) no-repeat;
        background-size: 60px 60px;
        text-align: center;
    }
    .clientlogo{
        float: left;
        height: 60px;
        width: 60px;
        background: url(http://michaeltruong.ca/images/client.jpg) no-repeat;
        background-size: 60px 60px;
        border-radius: 50px;
    }
    .info{
        display: block;
    //float:left;
        margin-left: 0;
    }
    .title{
        float: right;
    }
    .title p{text-align: right;}
    table{
        width: 100%;
        border-collapse: collapse;
    }
    td{
    //padding: 5px 0 5px 15px;
    //border: 1px solid #EEE
    }
    .tabletitle{
    //padding: 5px;
        font-size: .5em;
        background: #EEE;
    }
    .service{border-bottom: 1px solid #EEE;}
    .item{width: 24mm;}
    .itemtext{font-size: .5em;}

    #legalcopy{
        margin-top: 5mm;
    }
</style>
<div id="p2p_receipt">

    <center id="top">
        <div class="logo"></div>
        <div class="info">
            <h2>P2P Transfer Successful</h2>
        </div><!--End Info-->
    </center><!--End InvoiceTop-->

    <div id="mid">
        <div class="info">
            <h2>Transfer From</h2>
            <p>
                Name : {{$user->firstname.' '.$user->lastname}}</br>
                Email   : {{$user->email}}</br>
{{--                Phone   : {{$user->mobile}}</br>--}}
            </p>
        </div>
        <div class="info">
            <h2>Transfer To</h2>
            <p>
                Name : {{$otherUser->firstname.' '.$otherUser->lastname}}</br>
                Email   : {{$otherUser->email}}</br>
{{--                Phone   : {{$otherUser->mobile}}</br>--}}
            </p>
        </div>
    </div><!--End Invoice Mid-->

    <div id="bot">

        <div id="table">
            <p><b>TRX</b>: {{$transaction->trx}}</p>
            <p><b>Created At</b>: {{$transaction->created_at}}</p>
            <p><b>Transfer Amount</b>: {{$transaction->amount}}</p>
            <p><b>Available Balance</b>: {{showAmount($transaction->post_balance, 2)}}</p>
{{--            <p><b>Details</b>: {{$transaction->details}}</p>--}}
        </div><!--End Table-->

        <div id="legalcopy">
            <p class="legal">&nbsp;</p>
        </div>

    </div><!--End InvoiceBot-->
</div><!--End Invoice-->
