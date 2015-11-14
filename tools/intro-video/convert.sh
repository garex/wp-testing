mkdir -p mkv
ls -1 ogv/* | xargs -n 1 basename -s .ogv | xargs -I {} ffmpeg -y -i ogv/{}.ogv -c copy mkv/{}.mkv
