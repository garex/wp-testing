#!/usr/bin/env bash

ffmpeg -video_size 1616x1024 -framerate 24 -f x11grab -i :0.0+64,24 -c:v libx264 -qp 0 -preset ultrafast mkv/$(date +%s).mkv
