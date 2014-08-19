<?php

namespace ApiBird;

interface HandlerInterface
{
    public function fromFormat($data);

    public function toFormat($data);
}
