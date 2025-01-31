import React, { useEffect, useState } from 'react';
import 'regenerator-runtime/runtime';
import './QuadsAdSellList.scss';

const {__} = wp.i18n;

const formatDate = (dateString) => {
    const date = new Date(dateString);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are zero-based
    const year = date.getFullYear();
    return `${day}-${month}-${year}`;
  };

const AdSellRecords = () => {
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
            const endpoint = `${apiEndpoint}quads-route/list-adsell-records`;
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

    // Approve or Disapprove record
    const handleApproval = async (id, ad_status) => {
        try {
            const response = await fetch(`${apiEndpoint}quads-route/adsell/${id}/${ad_status}`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': quads_localize_data.nonce
                },
                body: JSON.stringify({ ad_status })
            });
            const result = await response.json();
            if (result.success) {
                setRecords((prevRecords) =>
                    prevRecords.map((record) =>
                        record.id === id ? { ...record, ad_status } : record
                    )
                );
            } else {
                console.error(`Error approving/disapproving record ${id}`, result);
            }
        } catch (error) {
            console.error(`Error updating record ${id}`, error);
        }
    };

    // Search handler
    const handleSearch = (e) => {
        setSearchTerm(e.target.value);
    };

    // Pagination logic
    const indexOfLastRecord = currentPage * recordsPerPage;
    const indexOfFirstRecord = indexOfLastRecord - recordsPerPage;
    const currentRecords = records.slice(indexOfFirstRecord, indexOfLastRecord);

    const totalPages = Math.ceil(totalRecords / recordsPerPage);
    let searchIcon = quads_localize_data.quads_plugin_url+'admin/assets/img/quads-search.png'; 
    return (
        <div className='sellable_ads_wrapper' style={{marginTop:'20px'}}>
        
            {currentRecords.length > 0 && (
                <div className="quads-search-box-panel">                
                    <div className="quads-search-box">
                            <input
                                style = {{ backgroundImage: `url(${searchIcon})`,          
                                backgroundRepeat: 'no-repeat',
                            }}
                            type="text"
                            placeholder={__('Search records...', 'quick-adsense-reloaded')}
                            className='quads-ad-search-box'
                            value={searchTerm}
                            onChange={handleSearch}
                        />
                    </div>   
              </div>   
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
                            <th>{__('AD Slot', 'quick-adsense-reloaded')}</th>
                            <th>{__('AD Contents', 'quick-adsense-reloaded')}</th>
                            <th>{__('Duration', 'quick-adsense-reloaded')}</th>
                            <th>{__('Status', 'quick-adsense-reloaded')}</th>
                            <th>{__('Action', 'quick-adsense-reloaded')}</th>
                        </tr>
                    </thead>
                )}
                    <tbody>
                    {currentRecords.map((record) => (
                        <tr key={record.id}>
                            <td>{record.id}</td>
                            <td>{record.ad_name}</td>
                            <td>
                                {record.ad_image ? (
                                    <img src={record.ad_image} alt={record.ad_name} width={'100px'}/>
                                ) : (
                                    //truncating the content to 50 characters
                                    record.ad_content.length > 50 ? record.ad_content.substring(0, 300) + '...' : record.ad_content
                                )}
                                <br/>Link : {record.ad_link}
                           </td>
                            <td>{formatDate(record.start_date)} - {formatDate(record.end_date)}</td>

                            <td>{record.ad_status}</td>
                            <td>
                                {record.ad_status == 'pending'  && (
                                    <button
                                        className='quads-btn quads-btn-primary'
                                        style={{padding:'5px 10px',fontSize:'14px'}}
                                        onClick={() => handleApproval(record.id, 'approved')}
                                    >
                                        {__('Approve', 'quick-adsense-reloaded')}
                                    </button>
                                    
                                )}
                                 {record.ad_status == 'pending' && (
                                    <button
                                    style={{padding:'5px 10px',fontSize:'14px'}}
                                    className='quads-btn quads-btn-primary'
                                    onClick={() => handleApproval(record.id, 'disapproved')}
                                >
                                    {__('Disapprove', 'quick-adsense-reloaded')}
                                </button>
                                    
                                )}
                                {record.ad_status == 'disapproved' && (
                                    <button
                                    style={{padding:'5px 10px',fontSize:'14px'}}
                                    className='quads-btn quads-btn-primary'
                                        onClick={() => handleApproval(record.id, 'approved')}
                                    >
                                        {__('Approve', 'quick-adsense-reloaded')}
                                    </button>
                                )}
                                 {record.ad_status == 'approved' && new Date(record.end_date) >= new Date() &&  (
                                    <button
                                    style={{padding:'5px 10px',fontSize:'14px'}}
                                    className='quads-btn quads-btn-primary'
                                        onClick={() => handleApproval(record.id, 'disapproved')}
                                    >
                                        {__('Disapprove', 'quick-adsense-reloaded')}
                                    </button>
                                )}

                                {record.ad_status == 'approved' && new Date(record.end_date) < new Date() && (
                                   __('AD Expired', 'quick-adsense-reloaded')
                                )}


                            </td>
                        </tr>
                    ))}

                    </tbody>
                </table>

            
            <div className='quads-pagination'>
               
                {currentRecords.length === 0 && (
                    <h2>{__('No ads have been purchased on your site as of now.', 'quick-adsense-reloaded')}</h2>
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

export default AdSellRecords;
