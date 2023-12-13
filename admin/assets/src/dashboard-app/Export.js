import { __ } from "@wordpress/i18n";

const Export = () => {
	return (
		<>
			<section className={ `astra-child-field block px-8 py-8 justify-between` }>
				<div className="w-full flex items-center">
					<h3 className="p-0 flex-1 inline-flex justify-right text-xl leading-6 font-semibold text-slate-800">
						{__("Export Settings", "astra-import-export")}
					</h3>
					<div className='flex justify-right items-center'>
						<form method="post" className="inline-flex ast-import-settings-form">
							<input type="hidden" name="astra_ie_action" value="export_settings" />
							<input type="hidden" id="astra_export_nonce" name="astra_export_nonce" value={ast_import_export_admin.astra_export_nonce}/>
							{
								ast_import_export_admin.header_footer_layout_caps &&
								<select
									id="ast_customizer_data_type_export"
									name="ast_customizer_data_type_export"
									className="ast_customizer_data_type mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6"
									defaultValue="all"
								>
									<option value="all"> {__("All Settings", "astra-import-export")} </option>
									<option value="header"> {__("Header", "astra-import-export")} </option>
									<option value="footer"> {__("Footer", "astra-import-export")} </option>
								</select>
							}
							<button
								type="submit"
								className="inline-flex px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-astra focus-visible:bg-astra-hover hover:bg-astra-hover focus:outline-none"
							>
								{__("Export", "astra-import-export")}
							</button>
						</form>
					</div>
				</div>

				<p className="mt-2 text-sm text-slate-600 w-9/12 tablet:w-full">
					{ __( `Export your current ${astra_admin.theme_name} Customizer settings.`, 'astra-import-export' ) }
				</p>
			</section>
		</>
	);
};

export default Export;
