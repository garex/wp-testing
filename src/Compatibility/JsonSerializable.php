<?php
if (!interface_exists('JsonSerializable')) {
    interface JsonSerializable {
        abstract public function jsonSerialize();
    }
}
