<?php
if (!interface_exists('JsonSerializable')) {
    interface JsonSerializable {
        function jsonSerialize();
    }
}
