import React, { useEffect, useState } from 'react';
import 'regenerator-runtime/runtime';
import './QuadsDisabledAdsList.scss';

const {__} = wp.i18n;

const formatDate = (dateString) => {
    const date = new Date(dateString);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are zero-based
    const year = date.getFullYear();
    return `${day}-${month}-${year}`;
  };

const QuadsDisabledAdsList = () => {
    const [records, setRecords] = useState([]);
    const [loading, setLoading] = useState(false);
    const [currentPage, setCurrentPage] = useState(1);
    const [recordsPerPage] = useState(10);
    const [searchTerm, setSearchTerm] = useState('');
    const [totalRecords, setTotalRecords] = useState(0);
    let apiEndpoint = quads_localize_data.rest_url;
    // Fetch records from the custom table
    useEffect(() => {
        const fetchRecords = async () => {
            setLoading(true);
            const endpoint = `${apiEndpoint}quads-route/list-disabledad-records`;
            try {
                const response = await fetch(`${endpoint}?page=${currentPage}&limit=${recordsPerPage}&search=${searchTerm}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-WP-Nonce': quads_localize_data.nonce
                    }
                });
        
                // Ensure the response is in the expected format
                const data = await response.json();
                if (Array.isArray(data.records)) {
                    setRecords(data.records);
                    setTotalRecords(data.total);
                } else {
                    console.error("Expected records to be an array", data);
                    setRecords([]); // Reset records if the format is incorrect
                }
            } catch (error) {
                console.error("Error fetching records", error);
                setRecords([]); // Reset records on error
            }
            setLoading(false);
        };
        fetchRecords();
    }, [apiEndpoint, currentPage, searchTerm]);

   

    // Search handler
    const handleSearch = (e) => {
        setSearchTerm(e.target.value);
    };

    // Pagination logic
    const indexOfLastRecord = currentPage * recordsPerPage;
    const indexOfFirstRecord = indexOfLastRecord - recordsPerPage;
    const currentRecords = records.slice(indexOfFirstRecord, indexOfLastRecord);

    const totalPages = Math.ceil(totalRecords / recordsPerPage);

    return (
        <div className='sellable_ads_wrapper'>
            <h1>  {__('Disabled Ads', 'quick-adsense-reloaded')}</h1>

            {currentRecords.length > 0 && (

            <input
                type="text"
                placeholder={__('Search records...', 'quick-adsense-reloaded')}
                className='quads-ad-search-box'
                value={searchTerm}
                onChange={handleSearch}
            />
            )}

            

            {/* Listing records */}
            {loading ? (
                 <div className="quads-cover-spin"></div>
            ) : (<div>
                <table className='quads-ad-table'>
                {currentRecords.length > 0 && (
                    <thead>
                        <tr>
                            <th>{__('ID', 'quick-adsense-reloaded')}</th>
                            <th>{__('Amount', 'quick-adsense-reloaded')}</th>
                            <th>{__('Duration', 'quick-adsense-reloaded')}</th>
                            <th>{__('User Info', 'quick-adsense-reloaded')}</th>
                            <th>{__('Status', 'quick-adsense-reloaded')}</th>
                            
                        </tr>
                    </thead>
                )}
                    <tbody>
                    {currentRecords.map((record) => (
                        <tr key={record.disable_ad_id}>
                            <td>{record.disable_ad_id}</td>
                            <td>{record.disable_cost}</td>
                            <td>{record.disable_duration}</td>
                            <td>
                                <p style={{margin:'0px'}}>{record.username}</p>
                                <p style={{margin:'0px'}}>{record.user_email}</p>
                            </td>
                            <td>{record.payment_status}</td>
                        </tr>
                    ))}

                    </tbody>
                </table>

            
            <div className='quads-pagination'>
               
                {currentRecords.length === 0 && (
                    <h2>{__('No Disable ads on your site as of now.', 'quick-adsense-reloaded')}</h2>
                )}
            {currentRecords.length > 0 && (
            <div className='pages'>
                {Array.from({ length: totalPages }, (_, index) => (
                    <button
                        key={index + 1}
                        onClick={() => setCurrentPage(index + 1)}
                        disabled={currentPage === index + 1}
                    >
                        {index + 1}
                    </button>
                ))}
            </div>
            )}
 {currentRecords.length > 0 && (
            <button
                onClick={() => setCurrentPage((prev) => Math.max(prev - 1, 1))}
                disabled={currentPage === 1}
            >
                {__('Previous', 'quick-adsense-reloaded')}
            </button>
            )}
             {currentRecords.length > 0 && (
            <button
                onClick={() => setCurrentPage((prev) => Math.min(prev + 1, totalPages))}
                disabled={currentPage === totalPages}
            >
                {__('Next', 'quick-adsense-reloaded')}
            </button>
            )}
        </div>
        </div>
            )}


        </div>
    );
};

export default QuadsDisabledAdsList;
