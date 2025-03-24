<?php

function passwd(string $password)
{
    return md5($password);
}
