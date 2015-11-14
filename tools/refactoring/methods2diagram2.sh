
HERE=`pwd`
cd ../../src

(
cat << EOF

digraph Calls {
  layout=fdp
  overlap=false
  splines=polyline
  edge [
    color="#6F715C"
    arrowhead=open
  ]
  node [
    style="rounded,filled"
    color="#F2CA52"
    shape=box
    fontname="Ubuntu"
    fontsize=10
  ]
EOF

ack -o 'wp->[a-zA-Z_]+\(' | sed -e 's/:/ /g' | awk '{ print $1 " " $3 }' | sort | uniq |\
sed -e 's/[()]//g' -e 's/.php//' |\
awk '{ print "  \"" $1 "\" [color=\"#C6DCE1\"] \n"  "  \"" $1 "\" -> \"" $2 "\"" }'
echo "}"
) | dot -Tsvg > $HERE/calls.svg