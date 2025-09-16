<?php
function get_weekdays()
{
    return [
        1 => '月',
        2 => '火',
        3 => '水',
        4 => '木',
        5 => '金',
        6 => '土',
        7 => '日',
    ];
}
function get_patientrepeat()
{
    return [
        0 => 'なし',
        1 => '毎日',
        2 => '毎週',
        3 => '隔週',
        4 => '毎月',
    ];
}
function get_patientcuretype()
{
    return [
        0 => 'なし',
        1 => '看護',
        2 => 'リハビリ',
    ];
}