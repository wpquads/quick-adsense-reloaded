import React, { Component, Fragment } from 'react';
import './QuadsAdListPagination.scss';

class QuadsAdListPagination extends Component {
    
  constructor(props) {      
    super(props);
    this.state = {         
        ad_count   :0,
        page_count :0
    };     
  }

  static getDerivedStateFromProps(props, state) { 
        let page_count = 0;        
        if(props.ad_list?.posts_found > 20){  
          page_count = Math.ceil(props.ad_list?.posts_found / 20);
        }
              
        return {
            page_count:page_count,
            ad_count : props.ad_list?.posts_found
        };
  }
  range(start, end){
    let length = end - start + 1;
    return Array.from({ length }, (_, idx) => idx + start);
  }
  paginationRange(totalCount, pageSize, siblingCount = 1, currentPage){
    const totalPageCount = Math.ceil(totalCount / pageSize);
    // Pages count is determined as siblingCount + firstPage + lastPage + currentPage + 2*DOTS
    const totalPageNumbers = siblingCount + 5;

    /*
      Case 1:
      If the number of pages is less than the page numbers we want to show in our
      paginationComponent, we return the range [1..totalPageCount]
    */
    if (totalPageNumbers >= totalPageCount) {
      return this.range(1, totalPageCount);
    }
	
    /*
    	Calculate left and right sibling index and make sure they are within range 1 and totalPageCount
    */
    const leftSiblingIndex = Math.max(currentPage - siblingCount, 1);
    const rightSiblingIndex = Math.min(currentPage + siblingCount,totalPageCount);

    /*
      We do not show dots just when there is just one page number to be inserted between the extremes of sibling and the page limits i.e 1 and totalPageCount. Hence we are using leftSiblingIndex > 2 and rightSiblingIndex < totalPageCount - 2
    */
    const shouldShowLeftDots = leftSiblingIndex > 2;
    const shouldShowRightDots = rightSiblingIndex < totalPageCount - 2;

    const firstPageIndex = 1;
    const lastPageIndex = totalPageCount;

    /*
    	Case 2: No left dots to show, but rights dots to be shown
    */
    if (!shouldShowLeftDots && shouldShowRightDots) {
      let leftItemCount = 3 + 2 * siblingCount;
      let leftRange = this.range(1, leftItemCount);
      return [...leftRange, 'DOTS', totalPageCount];
    }

    /*
    	Case 3: No right dots to show, but left dots to be shown
    */
    if (shouldShowLeftDots && !shouldShowRightDots) {
      
      let rightItemCount = 3 + 2 * siblingCount;
      let rightRange = this.range(
        totalPageCount - rightItemCount + 1,
        totalPageCount
      );
      return [firstPageIndex, 'DOTS', ...rightRange];
    }
     
    /*
    	Case 4: Both left and right dots to be shown
    */
    if (shouldShowLeftDots && shouldShowRightDots) {
      let middleRange = this.range(leftSiblingIndex, rightSiblingIndex);
      return [firstPageIndex, 'DOTS', ...middleRange, 'DOTS', lastPageIndex];
    }

    if (!shouldShowLeftDots && !shouldShowRightDots) {
      let leftItemCount = 3 + 2 * siblingCount;
      let leftRange = this.range(1, leftItemCount);
      return [...leftRange, 'DOTS', totalPageCount];
    }
  }

  render() {
    if(this.state.ad_count > 20){   
        const paginate = [];
        let pagination = [];
        pagination = this.paginationRange(this.state.ad_count, 20, 1 , this.props.ad_list.clicked_btn_id);
        pagination.map(pageNumber => {
          if (pageNumber === 'DOTS') {
            paginate.push(<a  key={pageNumber} data-index={pageNumber} data-id={pageNumber} href="#">&#8230;</a>);  
          }else{
            paginate.push(<a className={this.props.ad_list.clicked_btn_id == pageNumber ? 'quads-page-active' : ''} onClick={this.props.triggerPagination} key={pageNumber} data-index={pageNumber} data-id={pageNumber} href="#">{pageNumber}</a>);                 
          }
        });       
        return (
        <div className="quads-ads-pagination">
            <a className={this.props.ad_list.clicked_btn_id == 0 ? 'quads-page-active material-icons' : 'material-icons'} onClick={this.props.triggerPagination} key={0} data-index={0} data-id="1" href="#">keyboard_arrow_left</a>
            {paginate}
            <a className={(this.props.ad_list.clicked_btn_id == (this.state.page_count+1)) ? 'quads-page-active material-icons' : 'material-icons'} onClick={this.props.triggerPagination} data-index={(this.state.page_count+1)} key={(this.state.page_count+1)} data-id={this.state.page_count} href="#">keyboard_arrow_right</a>
        </div>
        
    );
            
    }else{
       
        return '';    
        
    }    
    
  }
  
}

export default QuadsAdListPagination;