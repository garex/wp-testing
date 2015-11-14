<?php
if (!interface_exists('JsonSerializable')) {
    interface JsonSerializable
    {

        /**
         * Specify data which should be serialized to JSON
         *
         * @return mixed data which can be serialized by json_encode,
         */
        public function jsonSerialize();
    }
}
