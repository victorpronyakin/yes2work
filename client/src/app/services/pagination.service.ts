import { Injectable } from '@angular/core';

@Injectable()
export class PaginationService {

  constructor() { }

  /**
   * Get pagination
   * @param {number} totalItems
   * @param {number} currentPage
   * @param {number} pageSize
   * @returns {{
   * totalItems: number;
   * currentPage: number | undefined;
   * pageSize: number | undefined;
   * totalPages: number;
   * startPage: number;
   * endPage: number;
   * startIndex: number;
   * endIndex: number;
   * pages: any[]}}
   */
  public getPager(totalItems: number, currentPage: number = 1, pageSize: number = 50) {
    let totalPages = Math.ceil(totalItems / pageSize);

    if (currentPage < 1) {
      currentPage = 1;
    } else if (currentPage > totalPages) {
      currentPage = totalPages;
    }

    let startPage: number, endPage: number;
    if (totalPages <= 5) {
      startPage = 1;
      endPage = totalPages;
    } else {
      if (currentPage <= 3) {
        startPage = 1;
        endPage = 5;
      } else if (currentPage + 1 >= totalPages) {
        startPage = totalPages - 4;
        endPage = totalPages;
      } else {
        startPage = currentPage - 2;
        endPage = currentPage + 2;
      }
    }

    let startIndex = (currentPage - 1) * pageSize;
    let endIndex = Math.min(startIndex + pageSize - 1, totalItems - 1);

    let pages = Array.from(Array((endPage + 1) - startPage).keys()).map(i => startPage + i);

    return {
      totalItems: totalItems,
      currentPage: currentPage,
      pageSize: pageSize,
      totalPages: totalPages,
      startPage: startPage,
      endPage: endPage,
      startIndex: startIndex,
      endIndex: endIndex,
      pages: pages
    };
  }

}
