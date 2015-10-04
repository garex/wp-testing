
HERE=`pwd`
cd ../../db/migrations/wp_testing

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

ack -o '[^$]+>[a-zA-Z_]+\(' | sed -e 's/:/ /g' | awk '{ print $1 " " $3 }' | sort | uniq |\
# egrep -v '(e->getMessage|get_adapter|this->execute|Base/Base|Base/AddMeta|updateMetaInExample|Base/UpdateData)' |\
# egrep -v '(DecodeFormulasSource|this->field|Base/MigrateColumn|Column|Base/MigrateTable|Base/TableDefinition)' |\
sed -e 's/[0-9]*_WpTesting_Migration_//' -e 's/[()]//g' -e 's/.php//' -e 's/this->//' |\
awk '{ print "  \"" $1 "\" [color=\"#C6DCE1\"] \n"  "  \"" $1 "\" -> \"" $2 "\"" }'
echo "}"
) | dot -Tsvg > $HERE/calls.svg