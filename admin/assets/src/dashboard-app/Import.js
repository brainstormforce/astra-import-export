import { __ } from "@wordpress/i18n";
import { FormFileUpload } from '@wordpress/components';

const Import = () => {
	return (
		<>
			<section className={ `astra-child-field block px-12 py-8 justify-between border-b border-t border-solid border-slate-200` }>
				<div className="w-full flex items-center">
					<h3 className="p-0 flex-1 inline-flex justify-right text-xl leading-6 font-semibold text-slate-800">
						{__("Import Settings", "astra")}
					</h3>
					<div className='flex justify-right items-center'>
						<form method="post" className="inline-flex ast-import-settings-form" encType="multipart/form-data">
							<input type="hidden" name="astra_ie_action" value="import_settings" />
							<input type="hidden" id="astra_import_nonce" name="astra_import_nonce" value={ast_import_export_admin.astra_import_nonce}/>
							<input type="file" name="import_file" className="block w-full p-1 text-sm font-normal text-slate-500 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-slate-500 focus:bg-white focus:border-blue-600 focus:outline-none" id="formFileSm"/>
							<button
								type="submit"
								className="inline-flex ml-2 px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-astra focus-visible:bg-astra-hover hover:bg-astra-hover focus:outline-none"
							>
								{__("Import", "astra")}
							</button>
						</form>
					</div>
				</div>

				<p className="mt-2 text-sm text-slate-600 w-9/12">
					{ __( `Import your ${astra_admin.theme_name} Customizer settings.`, 'astra-import-export' ) }
				</p>
			</section>
		</>
	);
};

export default Import;
