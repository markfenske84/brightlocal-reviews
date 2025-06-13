import './style.scss';
import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, SelectControl, ToggleControl, RangeControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';
import apiFetch from '@wordpress/api-fetch';
import { useState, useEffect } from '@wordpress/element';

// ADD_BRIGHTLOCAL_ICON_START
// BrightLocal brand icon (PNG)
import BrightlocalPNG from '../assets/brightlocal.png';

const BrightlocalIcon = (
    <img
        src={ BrightlocalPNG }
        alt="BrightLocal"
        style={{ width: 24, height: 24 }}
    />
);
// ADD_BRIGHTLOCAL_ICON_END

// Source icons mapping
const sourceIcons = {
    google: 'https://www.google.com/favicon.ico',
    facebook: 'https://www.facebook.com/favicon.ico',
    yelp: 'https://www.yelp.com/favicon.ico',
    tripadvisor: 'https://www.tripadvisor.com/favicon.ico',
    brightlocal: 'https://www.brightlocal.com/favicon.ico',
    yahoo: 'https://www.yahoo.com/favicon.ico',
    default: 'https://www.brightlocal.com/favicon.ico'
};

registerBlockType('brightlocal-reviews/reviews', {
    title: __('BrightLocal Reviews', 'brightlocal-reviews'),
    icon: BrightlocalIcon,
    category: 'widgets',
    attributes: {
        displayType: {
            type: 'string',
            default: 'grid'
        },
        showAuthor: {
            type: 'boolean',
            default: true
        },
        showDate: {
            type: 'boolean',
            default: true
        },
        showSource: {
            type: 'boolean',
            default: true
        },
        reviewLabel: {
            type: 'string',
            default: 'all'
        },
        limitItems: {
            type: 'boolean',
            default: false
        },
        itemsPerPage: {
            type: 'number',
            default: 3
        },
        showArrows: {
            type: 'boolean',
            default: true
        }
    },

    edit: function(props) {
        const { attributes, setAttributes } = props;
        const blockProps = useBlockProps();

        // Local state for editor pagination mimic
        const [editorReviews, setEditorReviews] = useState([]);
        const [currentPage, setCurrentPage] = useState(1);
        const [hasMore, setHasMore] = useState(true);

        // Fetch available review labels
        const labels = useSelect((select) => {
            return select(coreStore).getEntityRecords('taxonomy', 'bl_review_label', {
                per_page: -1,
                _fields: ['id', 'name', 'slug']
            });
        }, []);

        const reviews = useSelect((select) => {
            const { getEntityRecords } = select(coreStore);

            // Prepare base query
            const query = {
                post_type: 'bl-reviews',
                per_page: attributes.limitItems ? attributes.itemsPerPage : 100,
                _embed: true,
                context: 'edit',
                _fields: ['id', 'title', 'content', 'date', 'meta']
            };

            // Convert selected label slug to term ID (REST API expects IDs)
            if (attributes.reviewLabel && attributes.reviewLabel !== 'all') {
                const allLabels = getEntityRecords('taxonomy', 'bl_review_label', {
                    per_page: -1,
                    _fields: ['id', 'slug']
                });

                if (allLabels) {
                    const matched = allLabels.find((l) => l.slug === attributes.reviewLabel);
                    if (matched) {
                        query['bl_review_label'] = matched.id; // Pass ID, not slug
                    }
                }
            }

            return getEntityRecords('postType', 'bl-reviews', query);
        }, [attributes.reviewLabel, attributes.limitItems, attributes.itemsPerPage]);

        const isLoading = useSelect((select) => {
            return select(coreStore).isResolving('postType', 'bl-reviews');
        }, []);

        if (isLoading) {
            return <div {...blockProps}>{__('Loading reviews...', 'brightlocal-reviews')}</div>;
        }

        // Sync initial fetched reviews into local state whenever source reviews change
        useEffect(() => {
            setEditorReviews(reviews || []);
            setCurrentPage(1);
            setHasMore(true);
        }, [reviews, attributes.limitItems, attributes.itemsPerPage]);

        // Prepare label options for the dropdown
        const labelOptions = [
            { label: __('All Reviews', 'brightlocal-reviews'), value: 'all' }
        ];

        if (labels) {
            labels.forEach(label => {
                labelOptions.push({
                    label: label.name,
                    value: label.slug
                });
            });
        }

        const loadMoreReviews = () => {
            // Build REST query for next page
            const nextPage = currentPage + 1;
            const perPage = attributes.itemsPerPage;

            let path = `/wp/v2/bl-reviews?per_page=${perPage}&page=${nextPage}&context=edit&_embed=true`;

            // Handle label filter
            if (attributes.reviewLabel && attributes.reviewLabel !== 'all') {
                path += `&bl_review_label=${encodeURIComponent(attributes.reviewLabel)}`;
            }

            apiFetch({ path }).then((moreReviews) => {
                if (moreReviews && moreReviews.length > 0) {
                    setEditorReviews([...editorReviews, ...moreReviews]);
                    setCurrentPage(nextPage);
                    if (moreReviews.length < perPage) {
                        setHasMore(false);
                    }
                } else {
                    setHasMore(false);
                }
            }).catch(() => {
                setHasMore(false);
            });
        };

        const wrapperClass = `bl-reviews-wrapper bl-reviews-${attributes.displayType} ${attributes.displayType === 'carousel' ? 'bl-editor-preview' : ''}`;

        return [
            <InspectorControls>
                <PanelBody title={__('Display Settings', 'brightlocal-reviews')}>
                    <SelectControl
                        label={__('Review Group', 'brightlocal-reviews')}
                        value={attributes.reviewLabel}
                        options={labelOptions}
                        onChange={(value) => setAttributes({ reviewLabel: value })}
                    />
                    <SelectControl
                        label={__('Display Type', 'brightlocal-reviews')}
                        value={attributes.displayType}
                        options={[
                            { label: __('Grid', 'brightlocal-reviews'), value: 'grid' },
                            { label: __('List', 'brightlocal-reviews'), value: 'list' },
                            { label: __('Carousel', 'brightlocal-reviews'), value: 'carousel' } 
                        ]}
                        onChange={(value) => setAttributes({ displayType: value })}
                    />
                    <ToggleControl
                        label={__('Show Author', 'brightlocal-reviews')}
                        checked={attributes.showAuthor}
                        onChange={(value) => setAttributes({ showAuthor: value })}
                    />
                    <ToggleControl
                        label={__('Show Date', 'brightlocal-reviews')}
                        checked={attributes.showDate}
                        onChange={(value) => setAttributes({ showDate: value })}
                    />
                    <ToggleControl
                        label={__('Show Source', 'brightlocal-reviews')}
                        checked={attributes.showSource}
                        onChange={(value) => setAttributes({ showSource: value })}
                    />
                    <ToggleControl
                        label={__('Limit Reviews', 'brightlocal-reviews')}
                        checked={attributes.limitItems}
                        onChange={(value) => setAttributes({ limitItems: value })}
                    />
                    {attributes.displayType === 'carousel' && (
                        <ToggleControl
                            label={__('Show Navigation Arrows', 'brightlocal-reviews')}
                            checked={attributes.showArrows}
                            onChange={(value) => setAttributes({ showArrows: value })}
                        />
                    )}
                    {attributes.limitItems && (
                        <RangeControl
                            label={__('Reviews per page', 'brightlocal-reviews')}
                            value={attributes.itemsPerPage}
                            min={3}
                            max={30}
                            step={1}
                            onChange={(value) => setAttributes({ itemsPerPage: value })}
                        />
                    )}
                </PanelBody>
            </InspectorControls>,
            <div {...blockProps}>
                <div className={wrapperClass}>
                    { (attributes.limitItems ? editorReviews : reviews) && (attributes.limitItems ? editorReviews : reviews).length > 0 ? (
                        (attributes.limitItems ? editorReviews : reviews).map((review, idx) => {
                            const rating = review.meta?._bl_rating || 0;
                            const source = review.meta?._bl_source || '';
                            const reviewTitle = review.meta?._bl_title || '';
                            const reviewDate = review.meta?._bl_date || review.date;
                            const sourceLower = source.toLowerCase();
                            const sourceIcon = sourceIcons[sourceLower] || sourceIcons.default;
                            const contentLength = review.content.rendered.replace(/<[^>]*>/g, '').length;
                            const needsReadMore = contentLength > 200;

                            const reviewItemClass = `bl-review-item ${attributes.displayType === 'carousel' && idx === 0 ? 'active' : ''}`;

                            return (
                                <div key={review.id} className={reviewItemClass}>
                                    <div className="bl-review-header">
                                        <div className="bl-review-rating">
                                            {[...Array(5)].map((_, i) => (
                                                <span key={i} className={`star ${i < rating ? 'filled' : ''}`}>★</span>
                                            ))}
                                        </div>
                                        
                                        <div className="bl-review-meta-right">
                                            {attributes.showDate && reviewDate && (
                                                <span className="bl-review-date">
                                                    {new Date(reviewDate).toLocaleDateString()}
                                                </span>
                                            )}
                                            
                                            {attributes.showSource && source && (
                                                <span className="bl-review-source">
                                                    <img src={sourceIcon} alt={source} />
                                                    {source.charAt(0).toUpperCase() + source.slice(1)}
                                                </span>
                                            )}
                                        </div>
                                    </div>
                                    
                                    {attributes.showAuthor && review.title.rendered && (
                                        <div className="bl-review-author">
                                            {review.title.rendered}
                                        </div>
                                    )}
                                    
                                    <div className={`bl-review-content ${needsReadMore ? 'collapsed' : ''}`}
                                         dangerouslySetInnerHTML={{ __html: review.content.rendered }} />
                                    
                                    {needsReadMore && (
                                        <div className="bl-review-read-more">
                                            Read More
                                        </div>
                                    )}
                                </div>
                            );
                        })
                    ) : (
                        <p>{__('No reviews found.', 'brightlocal-reviews')}</p>
                    )}

                </div>

                {attributes.displayType !== 'carousel' && attributes.limitItems && hasMore && (
                    <button type="button" className="bl-reviews-load-more-editor" onClick={loadMoreReviews}>
                        {__('Load More', 'brightlocal-reviews')}
                    </button>
                )}
            </div>
        ];
    },

    save: function() {
        return null; // Dynamic block, render handled by PHP
    }
}); 