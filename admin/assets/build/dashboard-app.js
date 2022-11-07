!function(){"use strict";var e=window.wp.element,t=(window.React,window.wp.i18n),a=()=>(0,e.createElement)(e.Fragment,null,(0,e.createElement)("section",{className:"astra-child-field block px-12 py-8 justify-between border-b border-t border-solid border-slate-200"},(0,e.createElement)("div",{className:"w-full flex items-center"},(0,e.createElement)("h3",{className:"p-0 flex-1 inline-flex justify-right text-xl leading-6 font-semibold text-slate-800"},(0,t.__)("Import Settings","astra-import-export")),(0,e.createElement)("div",{className:"flex justify-right items-center"},(0,e.createElement)("form",{method:"post",className:"inline-flex ast-import-settings-form",encType:"multipart/form-data"},(0,e.createElement)("input",{type:"hidden",name:"astra_ie_action",value:"import_settings"}),(0,e.createElement)("input",{type:"hidden",id:"astra_import_nonce",name:"astra_import_nonce",value:ast_import_export_admin.astra_import_nonce}),(0,e.createElement)("input",{type:"file",name:"import_file",className:"block w-full p-1 text-sm font-normal text-slate-500 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-slate-500 focus:bg-white focus:border-blue-600 focus:outline-none",id:"formFileSm"}),(0,e.createElement)("button",{type:"submit",className:"inline-flex ml-2 px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-astra focus-visible:bg-astra-hover hover:bg-astra-hover focus:outline-none"},(0,t.__)("Import","astra-import-export"))))),(0,e.createElement)("p",{className:"mt-2 text-sm text-slate-600 w-9/12"},(0,t.__)(`Import your ${astra_admin.theme_name} Customizer settings.`,"astra-import-export")))),r=()=>(0,e.createElement)(e.Fragment,null,(0,e.createElement)("section",{className:"astra-child-field block px-12 py-8 justify-between"},(0,e.createElement)("div",{className:"w-full flex items-center"},(0,e.createElement)("h3",{className:"p-0 flex-1 inline-flex justify-right text-xl leading-6 font-semibold text-slate-800"},(0,t.__)("Export Settings","astra-import-export")),(0,e.createElement)("div",{className:"flex justify-right items-center"},(0,e.createElement)("form",{method:"post",className:"inline-flex ast-import-settings-form"},(0,e.createElement)("input",{type:"hidden",name:"astra_ie_action",value:"export_settings"}),(0,e.createElement)("input",{type:"hidden",id:"astra_export_nonce",name:"astra_export_nonce",value:ast_import_export_admin.astra_export_nonce}),(0,e.createElement)("button",{type:"submit",className:"inline-flex px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-astra focus-visible:bg-astra-hover hover:bg-astra-hover focus:outline-none"},(0,t.__)("Export","astra-import-export"))))),(0,e.createElement)("p",{className:"mt-2 text-sm text-slate-600 w-9/12"},(0,t.__)(`Export your current ${astra_admin.theme_name} Customizer settings.`,"astra-import-export"))));wp.hooks.addFilter("astra_dashboard.settings_screen_after_global-settings","astra_addon/dashboard_app",(function(t){return(0,e.createElement)(e.Fragment,null," ",(0,e.createElement)(a,null)," ",(0,e.createElement)(r,null)," ")}))}();