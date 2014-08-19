<?php

namespace ApiBird;

interface ExtensionInterface
{
    public function fromFormat($data);

    public function toFormat($data);
}
