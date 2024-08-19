<?php

namespace Aymanalhattami\YemeniPaymentGateways;

enum Status: string
{

    case Pending = 'pending';
    case Success = 'success';
    case Failed = 'failed';
    case Canceled = 'canceled';
}
