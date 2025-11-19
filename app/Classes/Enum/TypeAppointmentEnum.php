<?php

namespace App\Classes\Enum;

enum TypeAppointmentEnum : string {

    case INITIAL = "consulta inicial";
    case CONTROL = "control";
    case URGENCE = "urgencia";
}
