import{F as l}from"./Form.31283b49.js";import{_ as p,r as u,o as t,c,a as d,F as f,b as i,m as _}from"./app.a7c00f52.js";let n=new Date().getTime();const y={props:{model:{type:Object,required:!0},resource:{type:Object,required:!0}},layout:function(r,e){return Object.keys(e.props.errors).length===0&&(n=new Date().getTime()),r(this.resolveDefaultLayout(),()=>r(l,{key:n,model:e.props.model,model_name:e.props.model.exists?e.props.resource.model_name:e.props.resource.name},()=>e))}};function k(r,e,m,F,$,b){const a=u("FormHandler");return t(!0),c(f,null,d(m.model.fields,o=>(t(),i(a,_(o,{modelValue:r.$parent.form[o.name],"onUpdate:modelValue":s=>r.$parent.form[o.name]=s,form:r.$parent.form,key:o.name,name:o.name}),null,16,["modelValue","onUpdate:modelValue","form","name"]))),128)}const B=p(y,[["render",k]]);export{B as default};