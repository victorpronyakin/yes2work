import { Injectable, NgZone } from '@angular/core';
import { MapsAPILoader } from '@agm/core';
import {} from '@types/googlemaps';
import { FormArray, FormControl, FormGroup } from '@angular/forms';
import { ToastrService } from 'ngx-toastr';
import { Router } from '@angular/router';
import { AuthService } from './auth.service';
import { SettingsApiService } from './settings-api.service';
import { HttpClient } from '@angular/common/http';
import {AdminBadges, BusinessBadges, CandidateBadges, Role} from '../../entities/models';
import { IMultiSelectTexts } from 'angular-2-dropdown-multiselect';

@Injectable()
export class SharedService extends SettingsApiService{

  public visibleErrorVideoIcon = false;
  public visibleErrorProfileIcon = false;
  public preRouterLink: string;

  public componentForm = {
    street_number: 'short_name',
    route: 'long_name',
    locality: 'long_name',
    administrative_area_level_1: 'short_name',
    country: 'long_name',
    postal_code: 'short_name',
    sublocality_level_2: 'long_name',
  };

  public configQualificationLevels: IMultiSelectTexts = {
    checkAll: 'Select all',
    uncheckAll: 'Deselect all',
    checked: 'item selected',
    checkedPlural: 'items selected',
    defaultTitle: 'Qualification Level',
    allSelected: 'All selected - Qualification Level',
  };
  public configTertiaryEducations: IMultiSelectTexts = {
    checkAll: 'Select all',
    uncheckAll: 'Deselect all',
    checked: 'item selected',
    checkedPlural: 'items selected',
    defaultTitle: 'Tertiary Education Achievement',
    allSelected: 'All selected - Tertiary Education Achievement',
  };
  public configSpecialization: IMultiSelectTexts = {
    checkAll: 'Select all',
    uncheckAll: 'Deselect all',
    checked: 'item selected',
    checkedPlural: 'items selected',
    defaultTitle: 'Field',
    allSelected: 'All selected - Field',
  };
  public configYearsWorks: IMultiSelectTexts = {
    checkAll: 'Select all',
    uncheckAll: 'Deselect all',
    checked: 'item selected',
    checkedPlural: 'items selected',
    defaultTitle: 'Years of work experience',
    allSelected: 'All selected - Years of work experience',
  };
  public configGender: IMultiSelectTexts = {
    checkAll: 'Select all',
    uncheckAll: 'Deselect all',
    checked: 'item selected',
    checkedPlural: 'items selected',
    defaultTitle: 'Gender',
    allSelected: 'All selected - Gender',
  };
  public configAvailability: IMultiSelectTexts = {
    checkAll: 'Select all',
    uncheckAll: 'Deselect all',
    checked: 'item selected',
    checkedPlural: 'items selected',
    defaultTitle: 'Availability',
    allSelected: 'All selected - Availability',
  };
  public configSalary: IMultiSelectTexts = {
    checkAll: 'Select all',
    uncheckAll: 'Deselect all',
    checked: 'item selected',
    checkedPlural: 'items selected',
    defaultTitle: 'Most Recent Monthly Salary',
    allSelected: 'All selected - Most Recent Monthly Salary',
  };
  public configEthnicity: IMultiSelectTexts = {
    checkAll: 'Select all',
    uncheckAll: 'Deselect all',
    checked: 'item selected',
    checkedPlural: 'items selected',
    defaultTitle: 'Ethnicity',
    allSelected: 'All selected - Ethnicity',
  };
  public configLocation: IMultiSelectTexts = {
    checkAll: 'Select all',
    uncheckAll: 'Deselect all',
    checked: 'item selected',
    checkedPlural: 'items selected',
    defaultTitle: 'Location',
    allSelected: 'All selected - Location',
  };
  public articlesFirmTextConfig: IMultiSelectTexts = {
    checkAll: 'Select all',
    uncheckAll: 'Deselect all',
    checked: 'item selected',
    checkedPlural: 'items selected',
    defaultTitle: 'Articles firm',
    allSelected: 'All selected - Articles firm',
  };

  public qualificationData = {
    tertiary_institution: [
      { value: 'AAA School of Advertising' },
      { value: 'Advanced Technology Training Institution' },
      { value: 'AFDA Film, TV and Perfomance School (AFDA)' },
      { value: 'Arise Business College' },
      { value: 'Auckland Park Theological (ATS)' },
      { value: 'Baptist Theological College' },
      { value: 'Boland FET College' },
      { value: 'Boston City Campus and Business College' },
      { value: 'Buffalo City FET Public College' },
      { value: 'Bytes People Solution' },
      { value: 'Cape Peninsula University of Technology (CPUT)' },
      { value: 'Capricorn College for FET (Capricorn College)' },
      { value: 'Central Johannesburg College' },
      { value: 'Central University of Technology' },
      { value: 'Cida City Campus' },
      { value: 'CITMA College' },
      { value: 'Coastal FET College (Mobeni)' },
      { value: 'College of Cape Town FET College' },
      { value: 'Computer Training Institude (CTI Education Group)' },
      { value: 'Cornerstone' },
      { value: 'Cornerstone Institute' },
      { value: 'Damelin' },
      { value: 'Durban Computer College' },
      { value: 'Durban University of Technology (DUT)' },
      { value: 'Eastcape Midlands FET College' },
      { value: 'Ehlanzeni FET College' },
      { value: 'Ekurhuleni East FET College' },
      { value: 'Ekurhuleni West College' },
      { value: 'Elangeni FET College' },
      { value: 'Esayidi FET College' },
      { value: 'False Bay FET College' },
      { value: 'Flavius Mareka FET College' },
      { value: 'George Whitefield College' },
      { value: 'Gert Sibande FET College (GS College)' },
      { value: 'Goldfields FET College (GFC)' },
      { value: 'Helderberg College' },
      { value: 'Ikhala Public FET College' },
      { value: 'IMM Graduate School of Marketing (IMM GSM)' },
      { value: 'Ingwe FET College' },
      { value: 'Inscape Design College' },
      { value: 'Jeppe College of Commerce and Computer Studies' },
      { value: 'King Hintsa FET College' },
      { value: 'King Sabata Dalindyebo FET College' },
      { value: 'Lephalale FET College' },
      { value: 'Letaba FET College' },
      { value: 'Lovedale Public FET College' },
      { value: 'Luton Business College' },
      { value: 'Majuba FET College' },
      { value: 'Maluti FET College' },
      { value: 'Management College of Southern Africa (MANCOSA)' },
      { value: 'Midrand Graduate Institute (MGI)' },
      { value: 'Milpark Business School' },
      { value: 'Mnambithi FET College' },
      { value: 'Monash South Africa' },
      { value: 'Mongosuthu University of Technology (MUT)' },
      { value: 'Mopani South East FET College' },
      { value: 'Motheo FET College' },
      { value: 'MSC Business College' },
      { value: 'Mthashana FET College' },
      { value: 'National University of Lesotho' },
      { value: 'Nelson Mandela Metropolitan University (NMMU)' },
      { value: 'Nkangala FET College' },
      { value: 'North West University' },
      { value: 'Northern Cape Rural FET College' },
      { value: 'Northern Cape Urban FET College' },
      { value: 'Northlink FET College' },
      { value: 'Orbit FET College' },
      { value: 'Oval Education International' },
      { value: 'PC and Business College' },
      { value: 'Port Elizabeth FET College' },
      { value: 'Prestige Academy' },
      { value: 'Regent Business School' },
      { value: 'Rhodes University' },
      { value: 'Ropzide IT Training Centre' },
      { value: 'Rosebank College' },
      { value: 'School of Tourism and Hospitality' },
      { value: 'Sedibeng FET College' },
      { value: 'Sekhukhune FET College' },
      { value: 'South African Theological Seminary (SATS)' },
      { value: 'South Cape FET College' },
      { value: 'South West Gauteng College' },
      { value: 'St Augustine College of South Africa' },
      { value: 'Stanford Computer and Business College' },
      { value: 'Stellenbosch University' },
      { value: 'Stenden South Africa' },
      { value: 'Taletso FET College' },
      { value: 'The Digital Marketing Institute' },
      { value: 'The Open Window School of Visual Communication' },
      { value: 'The Tertiary School in Business administration (TSIBA)' },
      { value: 'Thekwini FET College' },
      { value: 'Tshwane North FET College' },
      { value: 'Tshwane South FET College' },
      { value: 'Tshwane University of Technology (TUT)' },
      { value: 'Umfolozi FET College' },
      { value: 'Umgungundlovu FET College' },
      { value: 'University of Cape Town (UCT)' },
      { value: 'University of Fort Hare' },
      { value: 'University of Johannesburg' },
      { value: 'University of KwaZulu-Natal' },
      { value: 'University of Limpopo' },
      { value: 'University of Pretoria' },
      { value: 'University of South Africa (UNISA)' },
      { value: 'University of the Free State' },
      { value: 'University of the Western Cape (UWC)' },
      { value: 'University of the Witwatersrand (Witts)' },
      { value: 'University of Venda' },
      { value: 'University of Zululand' },
      { value: 'Vaal University of Technology' },
      { value: 'Varsity College' },
      { value: 'Vega -School of Brand Leadership' },
      { value: 'Vhembe FET College' },
      { value: 'Victory Training College' },
      { value: 'Vine College' },
      { value: 'Vuselela FET College' },
      { value: 'Walter Sisulu University (WSU)' },
      { value: 'Waterberg FET College' },
      { value: 'West Coast College' },
      { value: 'Western College FET' },
      { value: 'Other' }
    ],
    qualification_level: [
      { value: 'NQF 5 - Higher Certificate' },
      { value: 'NQF 6 - Diploma' },
      { value: 'NQF 7 - Degree' },
      { value: 'NQF 8 - Honours' },
      { value: 'NQF 9 - Masters' },
      { value: 'NQF 10 - PhD' }
    ],
    specific_qualification: [
      { value: 'Advanced Certificate in Education and Social Science' },
      { value: 'Bachelor Of Education' },
      { value: 'Bachelor Of Education Honours' },
      { value: 'Bachelor in Agricultural Economics and Management (BScAgric or BAgricAdmin)' },
      { value: 'Bachelor of Accounting Science' },
      { value: 'Bachelor of Architectural Studies' },
      { value: 'Bachelor of Architectural Studies Honours' },
      { value: 'Bachelor of Arts' },
      { value: 'Bachelor of Arts Honours' },
      { value: 'Bachelor of Business Science' },
      { value: 'Bachelor of Business Science in Actuarial Science' },
      { value: 'Bachelor of Clinical Medical Practice' },
      { value: 'Bachelor of Commerce' },
      { value: 'Bachelor of Commerce Honours' },
      { value: 'Bachelor of Commerce in Actuarial Science' },
      { value: 'Bachelor of Dental Science' },
      { value: 'Bachelor of Economic Science -' },
      { value: 'Bachelor of Health Sciences' },
      { value: 'Bachelor of Laws (LLB)' },
      { value: 'Bachelor of Medicine and Bachelor of Surgery' },
      { value: 'Bachelor of Music' },
      { value: 'Bachelor of Music Honours' },
      { value: 'Bachelor of Music in Dance' },
      { value: 'Bachelor of Music: Composition' },
      { value: 'Bachelor of Music: Education' },
      { value: 'Bachelor of Music: General Programme' },
      { value: 'Bachelor of Music: Musicology' },
      { value: 'Bachelor of Nursing' },
      { value: 'Bachelor of Oral Health' },
      { value: 'Bachelor of Pharmacy' },
      { value: 'Bachelor of Science' },
      { value: 'Bachelor of Science Honours' },
      { value: 'Bachelor of Science Medicine (Honours) (BSc (Med)(Hons))' },
      { value: 'Bachelor of Science in Audiology' },
      { value: 'Bachelor of Science in Construction Studies' },
      { value: 'Bachelor of Science in Engineering' },
      { value: 'Bachelor of Science in Geomatics' },
      { value: 'Bachelor of Science in Occupational Therapy' },
      { value: 'Bachelor of Science in Physiotherapy' },
      { value: 'Bachelor of Science in Property Studies' },
      { value: 'Bachelor of Science in Speech-Language Pathology' },
      { value: 'Bachelor of Social Science' },
      { value: 'Bachelor of Social Science Honours' },
      { value: 'Bachelor of Social Science in Philosophy, Politics and Economics' },
      { value: 'Bachelor of Social Work' },
      { value: 'Bachelor of Technology in Applied Science' },
      { value: 'Bachelor of Technology in Business' },
      { value: 'Bachelor of Technology in Engineering' },
      { value: 'Bachelor of Technology in Health and Wellness Science' },
      { value: 'Bachelor of Technology in Informatics and design' },
      { value: 'Doctor of Architecture' },
      { value: 'Doctor of Economic Sciences' },
      { value: 'Doctor of Education' },
      { value: 'Doctor of Fine Art' },
      { value: 'Doctor of Laws' },
      { value: 'Doctor of Literature' },
      { value: 'Doctor of Medicine (MD)' },
      { value: 'Doctor of Music' },
      { value: 'Doctor of Philosophy' },
      { value: 'Doctor of Philosophy (Commerce)' },
      { value: 'Doctor of Philosophy (Law)' },
      { value: 'Doctor of Philosophy (Science)' },
      { value: 'Doctor of Science in Engineering' },
      { value: 'Doctor of Science in Medicine (DSc(Med))' },
      { value: 'Doctor of Social Science' },
      { value: 'Doctoral in Agricultural Economics and Management (DSc Agric)' },
      { value: 'Doctoral in Agricultural Economics and Management (PhD Agric)' },
      { value: 'Doctoral in Agronomy (DSc Agric)' },
      { value: 'Doctoral in Agronomy (PhD Agric)' },
      { value: 'Doctoral in Conservation Ecology' },
      { value: 'Doctoral in Entomology' },
      { value: 'Doctoral in Forestry and Natural Resource Sciences (DScFor)' },
      { value: 'Doctoral in Forestry and Natural Resource Sciences (PhDFor)' },
      { value: 'Master in Agricultural Economics and Management (MScAgric, MAgricAdmin)' },
      { value: 'Master of Architecture' },
      { value: 'Master of Arts' },
      { value: 'Master of Arts in Fine Art' },
      { value: 'Master of Business Administration' },
      { value: 'Master of Business Administration in the Executive Programme' },
      { value: 'Master of Business Science' },
      { value: 'Master of City and Regional Planning' },
      { value: 'Master of City Planning and Urban Design' },
      { value: 'Master of Commerce' },
      { value: 'Master of Dental Surgery' },
      { value: 'Master of Education' },
      { value: 'Master of Engineering' },
      { value: 'Master of Family Medicine and Primary Care (MFamMed)' },
      { value: 'Master of Fine Art' },
      { value: 'Master of Landscape Architecture' },
      { value: 'Master of Law' },
      { value: 'Master of Library and Information Science' },
      { value: 'Master of Medicine (Specialty Training) (MMed)' },
      { value: 'Master of Nursing' },
      { value: 'Master of Philosophy' },
      { value: 'Master of Philosophy (Engineering)' },
      { value: 'Master of Philosophy (Health Sciences)' },
      { value: 'Master of Philosophy (Law)' },
      { value: 'Master of Philosophy (Science)' },
      { value: 'Master of Philosophy in Education' },
      { value: 'Master of Public Administration' },
      { value: 'Master of Public Health (MPH)' },
      { value: 'Master of Science' },
      { value: 'Master of Science (Engineering)' },
      { value: 'Master of Science (Health Sciences)' },
      { value: 'Master of Science in Dentistry' },
      { value: 'Master of Science in Medicine (MSc(Med))' },
      { value: 'Master of Social Science' },
      { value: 'National Certificate in Engineering' },
      { value: 'National Certificate in Health and Wellness Science' },
      { value: 'National Certificate in Informatics and Design' },
      { value: 'National Diploma in Applied Science' },
      { value: 'National Diploma in Business' },
      { value: 'National Diploma in Engineering' },
      { value: 'National Diploma in Health and wellness Science' },
      { value: 'National Diploma in Informatics and Design' },
      { value: 'National Diploma in Tourism Management' },
      { value: 'National Higher Certificate in Business' },
      { value: 'National Professional Diploma in Education and Social Science' },
      { value: 'Postgraduate Certificate in Education' },
      { value: 'Postgraduate Diploma in African Studies' },
      { value: 'Postgraduate Diploma in Art' },
      { value: 'Postgraduate Diploma in Dentistry' },
      { value: 'Postgraduate Diploma in Education' },
      { value: 'Postgraduate Diploma in Engineering' },
      { value: 'Postgraduate Diploma in Engineering Management' },
      { value: 'Postgraduate Diploma in Law' },
      { value: 'Postgraduate Diploma in Library and Information Science' },
      { value: 'Postgraduate Diploma in Management Studies' },
      { value: 'Postgraduate Diploma in Management in Management Practice' },
      { value: 'Postgraduate Diploma in Music in Performance' },
      { value: 'Postgraduate Diploma in Project Management' },
      { value: 'Postgraduate Diploma in Property Studies' },
      { value: 'Postgraduate Diploma in Transport Studies' },
      { value: 'Postgraduate diploma in Health Sciences' },
      { value: 'Other' }
    ],
    specialization: [
      { key: 'Bachelor of Accounting Science', value: 'Accounting and Finance' },
      { key: 'Bachelor of Business Science', value: 'Accounting and Finance' },
      { key: 'Master of Business Science', value: 'Accounting and Finance' },
      { key: 'Bachelor of Business Science in Actuarial Science', value: 'Actuarial Science and Statistics' },
      { key: 'Bachelor of Commerce in Actuarial Science', value: 'Actuarial Science and Statistics' },
      { key: 'Bachelor in Agricultural Economics and Management (BScAgric or BAgricAdmin)', value: 'Agriculture, Agronomy and Viticulture' },
      { key: 'Doctoral in Agricultural Economics and Management (DSc Agric)', value: 'Agriculture, Agronomy and Viticulture' },
      { key: 'Doctoral in Agricultural Economics and Management (PhD Agric)', value: 'Agriculture, Agronomy and Viticulture' },
      { key: 'Doctoral in Agronomy (DSc Agric)', value: 'Agriculture, Agronomy and Viticulture' },
      { key: 'Doctoral in Agronomy (PhD Agric)', value: 'Agriculture, Agronomy and Viticulture' },
      { key: 'Master in Agricultural Economics and Management (MScAgric, MAgricAdmin)', value: 'Agriculture, Agronomy and Viticulture' },
      { key: 'Bachelor of Science', value: 'Applied Science: Physics and Chemistry' },
      { key: 'Bachelor of Science Honours', value: 'Applied Science: Physics and Chemistry' },
      { key: 'Bachelor of Technology in Applied Science', value: 'Applied Science: Physics and Chemistry' },
      { key: 'Doctor of Philosophy (Science)', value: 'Applied Science: Physics and Chemistry' },
      { key: 'Master of Philosophy (Science)', value: 'Applied Science: Physics and Chemistry' },
      { key: 'Master of Science', value: 'Applied Science: Physics and Chemistry' },
      { key: 'National Diploma in Applied Science', value: 'Applied Science: Physics and Chemistry' },
      { key: 'Bachelor of Architectural Studies', value: 'Architecture and Interior Design' },
      { key: 'Bachelor of Architectural Studies Honours', value: 'Architecture and Interior Design' },
      { key: 'Doctor of Architecture', value: 'Architecture and Interior Design' },
      { key: 'Master of Architecture', value: 'Architecture and Interior Design' },
      { key: 'Bachelor of Arts', value: 'Art, Photogrpaphy, Drama, Music and Dance' },
      { key: 'Bachelor of Arts Honours', value: 'Art, Photogrpaphy, Drama, Music and Dance' },
      { key: 'Bachelor of Music', value: 'Art, Photogrpaphy, Drama, Music and Dance' },
      { key: 'Bachelor of Music Honours', value: 'Art, Photogrpaphy, Drama, Music and Dance' },
      { key: 'Bachelor of Music in Dance', value: 'Art, Photogrpaphy, Drama, Music and Dance' },
      { key: 'Bachelor of Music: Composition', value: 'Art, Photogrpaphy, Drama, Music and Dance' },
      { key: 'Bachelor of Music: Education', value: 'Art, Photogrpaphy, Drama, Music and Dance' },
      { key: 'Bachelor of Music: General Programme', value: 'Art, Photogrpaphy, Drama, Music and Dance' },
      { key: 'Bachelor of Music: Musicology', value: 'Art, Photogrpaphy, Drama, Music and Dance' },
      { key: 'Doctor of Fine Art', value: 'Art, Photogrpaphy, Drama, Music and Dance' },
      { key: 'Doctor of Music', value: 'Art, Photogrpaphy, Drama, Music and Dance' },
      { key: 'Doctor of Social Science', value: 'Art, Photogrpaphy, Drama, Music and Dance' },
      { key: 'Master of Arts', value: 'Art, Photogrpaphy, Drama, Music and Dance' },
      { key: 'Master of Arts in Fine Art', value: 'Art, Photogrpaphy, Drama, Music and Dance' },
      { key: 'Master of Fine Art', value: 'Art, Photogrpaphy, Drama, Music and Dance' },
      { key: 'Postgraduate Diploma in Art', value: 'Art, Photogrpaphy, Drama, Music and Dance' },
      { key: 'Postgraduate Diploma in Music in Performance', value: 'Art, Photogrpaphy, Drama, Music and Dance' },
      { key: 'Doctoral in Entomology', value: 'Biology' },
      { key: 'Bachelor of Commerce', value: 'Business and Management Studies' },
      { key: 'Bachelor of Commerce Honours', value: 'Business and Management Studies' },
      { key: 'Bachelor of Technology in Business', value: 'Business and Management Studies' },
      { key: 'Doctor of Philosophy (Commerce)', value: 'Business and Management Studies' },
      { key: 'Master of Business Administration', value: 'Business and Management Studies' },
      { key: 'Master of Business Administration in the Executive Programme', value: 'Business and Management Studies' },
      { key: 'Master of Commerce', value: 'Business and Management Studies' },
      { key: 'National Diploma in Business', value: 'Business and Management Studies' },
      { key: 'National Higher Certificate in Business', value: 'Business and Management Studies' },
      { key: 'Postgraduate Diploma in Management Studies', value: 'Business and Management Studies' },
      { key: 'Postgraduate Diploma in Management in Management Practice', value: 'Business and Management Studies' },
      { key: 'Master of City and Regional Planning', value: 'City, Regional and Town Planning' },
      { key: 'Master of City Planning and Urban Design', value: 'City, Regional and Town Planning' },
      { key: 'Bachelor of Technology in Informatics and design', value: 'Computer Science: IT, Programming, informatics, Data Analytics and Systems' },
      { key: 'Master of Library and Information Science', value: 'Computer Science: IT, Programming, informatics, Data Analytics and Systems' },
      { key: 'National Certificate in Informatics and Design', value: 'Computer Science: IT, Programming, informatics, Data Analytics and Systems' },
      { key: 'National Diploma in Informatics and Design', value: 'Computer Science: IT, Programming, informatics, Data Analytics and Systems' },
      { key: 'Postgraduate Diploma in Library and Information Science', value: 'Computer Science: IT, Programming, informatics, Data Analytics and Systems' },
      { key: 'Doctoral in Conservation Ecology', value: 'Conservation, Environmental Science and Ecology' },
      { key: 'Advanced Certificate in Education and Social Science', value: 'Education and Social Sciences' },
      { key: 'Bachelor Of Education', value: 'Education and Social Sciences' },
      { key: 'Bachelor Of Education Honours', value: 'Education and Social Sciences' },
      { key: 'Bachelor of Social Science', value: 'Education and Social Sciences' },
      { key: 'Bachelor of Social Science Honours', value: 'Education and Social Sciences' },
      { key: 'Bachelor of Social Work', value: 'Education and Social Sciences' },
      { key: 'Doctor of Education', value: 'Education and Social Sciences' },
      { key: 'Master of Education', value: 'Education and Social Sciences' },
      { key: 'Master of Philosophy in Education', value: 'Education and Social Sciences' },
      { key: 'Master of Social Science', value: 'Education and Social Sciences' },
      { key: 'National Professional Diploma in Education and Social Science', value: 'Education and Social Sciences' },
      { key: 'Postgraduate Certificate in Education', value: 'Education and Social Sciences' },
      { key: 'Postgraduate Diploma in African Studies', value: 'Education and Social Sciences' },
      { key: 'Postgraduate Diploma in Education', value: 'Education and Social Sciences' },
      { key: 'Bachelor of Science in Engineering', value: 'Engineering (Chemical, Civil, Electronic, Mechatronic, Electrical and Industrial)' },
      { key: 'Bachelor of Technology in Engineering', value: 'Engineering (Chemical, Civil, Electronic, Mechatronic, Electrical and Industrial)' },
      { key: 'Doctor of Science in Engineering', value: 'Engineering (Chemical, Civil, Electronic, Mechatronic, Electrical and Industrial)' },
      { key: 'Master of Engineering', value: 'Engineering (Chemical, Civil, Electronic, Mechatronic, Electrical and Industrial)' },
      { key: 'Master of Philosophy (Engineering)', value: 'Engineering (Chemical, Civil, Electronic, Mechatronic, Electrical and Industrial)' },
      { key: 'Master of Science (Engineering)', value: 'Engineering (Chemical, Civil, Electronic, Mechatronic, Electrical and Industrial)' },
      { key: 'National Certificate in Engineering', value: 'Engineering (Chemical, Civil, Electronic, Mechatronic, Electrical and Industrial)' },
      { key: 'National Diploma in Engineering', value: 'Engineering (Chemical, Civil, Electronic, Mechatronic, Electrical and Industrial)' },
      { key: 'Postgraduate Diploma in Engineering', value: 'Engineering (Chemical, Civil, Electronic, Mechatronic, Electrical and Industrial)' },
      { key: 'Postgraduate Diploma in Engineering Management', value: 'Engineering (Chemical, Civil, Electronic, Mechatronic, Electrical and Industrial)' },
      { key: 'Doctoral in Forestry and Natural Resource Sciences (DScFor)', value: 'Forestry and Natural Resource Science' },
      { key: 'Doctoral in Forestry and Natural Resource Sciences (PhDFor)', value: 'Forestry and Natural Resource Science' },
      { key: 'Bachelor of Clinical Medical Practice', value: 'Health Sciences: Medical, Nursing and Pharmacology' },
      { key: 'Bachelor of Health Sciences', value: 'Health Sciences: Medical, Nursing and Pharmacology' },
      { key: 'Bachelor of Medicine and Bachelor of Surgery', value: 'Health Sciences: Medical, Nursing and Pharmacology' },
      { key: 'Bachelor of Nursing', value: 'Health Sciences: Medical, Nursing and Pharmacology' },
      { key: 'Bachelor of Pharmacy', value: 'Health Sciences: Medical, Nursing and Pharmacology' },
      { key: 'Bachelor of Science Medicine (Honours) (BSc (Med)(Hons))', value: 'Health Sciences: Medical, Nursing and Pharmacology' },
      { key: 'Bachelor of Science in Occupational Therapy', value: 'Health Sciences: Medical, Nursing and Pharmacology' },
      { key: 'Bachelor of Science in Speech-Language Pathology', value: 'Health Sciences: Medical, Nursing and Pharmacology' },
      { key: 'Bachelor of Technology in Health and Wellness Science', value: 'Health Sciences: Medical, Nursing and Pharmacology' },
      { key: 'Doctor of Medicine (MD)', value: 'Health Sciences: Medical, Nursing and Pharmacology' },
      { key: 'Doctor of Science in Medicine (DSc(Med))', value: 'Health Sciences: Medical, Nursing and Pharmacology' },
      { key: 'Master of Family Medicine and Primary Care (MFamMed)', value: 'Health Sciences: Medical, Nursing and Pharmacology' },
      { key: 'Master of Medicine (Specialty Training) (MMed)', value: 'Health Sciences: Medical, Nursing and Pharmacology' },
      { key: 'Master of Nursing', value: 'Health Sciences: Medical, Nursing and Pharmacology' },
      { key: 'Master of Philosophy (Health Sciences)', value: 'Health Sciences: Medical, Nursing and Pharmacology' },
      { key: 'Master of Public Health (MPH)', value: 'Health Sciences: Medical, Nursing and Pharmacology' },
      { key: 'Master of Science (Health Sciences)', value: 'Health Sciences: Medical, Nursing and Pharmacology' },
      { key: 'Master of Science in Medicine (MSc(Med))', value: 'Health Sciences: Medical, Nursing and Pharmacology' },
      { key: 'National Certificate in Health and Wellness Science', value: 'Health Sciences: Medical, Nursing and Pharmacology' },
      { key: 'National Diploma in Health and wellness Science', value: 'Health Sciences: Medical, Nursing and Pharmacology' },
      { key: 'Postgraduate diploma in Health Sciences', value: 'Health Sciences: Medical, Nursing and Pharmacology' },
      { key: 'Doctor of Literature', value: 'Journalism, Language and Literature' },
      { key: 'Master of Landscape Architecture', value: 'Landscape and Horticulture' },
      { key: 'Bachelor of Laws (LLB)', value: 'Law' },
      { key: 'Doctor of Laws', value: 'Law' },
      { key: 'Doctor of Philosophy (Law)', value: 'Law' },
      { key: 'Master of Law', value: 'Law' },
      { key: 'Master of Philosophy (Law)', value: 'Law' },
      { key: 'Postgraduate Diploma in Law', value: 'Law' },
      { key: 'Postgraduate Diploma in Transport Studies', value: 'Logistics, Transport and Supply Chain Management' },
      { key: 'Bachelor of Dental Science', value: 'Optometry, Audiology, Dentistry and Orthodontic' },
      { key: 'Bachelor of Oral Health', value: 'Optometry, Audiology, Dentistry and Orthodontic' },
      { key: 'Bachelor of Science in Audiology', value: 'Optometry, Audiology, Dentistry and Orthodontic' },
      { key: 'Master of Dental Surgery', value: 'Optometry, Audiology, Dentistry and Orthodontic' },
      { key: 'Master of Science in Dentistry', value: 'Optometry, Audiology, Dentistry and Orthodontic' },
      { key: 'Postgraduate Diploma in Dentistry', value: 'Optometry, Audiology, Dentistry and Orthodontic' },
      { key: 'Bachelor of Economic Science -', value: 'Philosophy, Politics and Economics' },
      { key: 'Bachelor of Social Science in Philosophy, Politics and Economics', value: 'Philosophy, Politics and Economics' },
      { key: 'Doctor of Economic Sciences', value: 'Philosophy, Politics and Economics' },
      { key: 'Doctor of Philosophy', value: 'Philosophy, Politics and Economics' },
      { key: 'Master of Philosophy', value: 'Philosophy, Politics and Economics' },
      { key: 'Bachelor of Science in Physiotherapy', value: 'Physiotherapy and Exercise Science' },
      { key: 'Postgraduate Diploma in Project Management', value: 'Project Management' },
      { key: 'Bachelor of Science in Construction Studies', value: 'Property and Construction Studies and Quantity Surverying' },
      { key: 'Bachelor of Science in Geomatics', value: 'Property and Construction Studies and Quantity Surverying' },
      { key: 'Bachelor of Science in Property Studies', value: 'Property and Construction Studies and Quantity Surverying' },
      { key: 'Postgraduate Diploma in Property Studies', value: 'Property and Construction Studies and Quantity Surverying' },
      { key: 'Master of Public Administration', value: 'Public Sector' },
      { key: 'National Diploma in Tourism Management', value: 'Travel, Tourism and Hospitality Management' },
      { key: 'Other', value: 'Other' }
    ]
  };

  public specializationCandidate = [
    { id: 'Accounting and Finance', name: 'Accounting and Finance' },
    { id: 'Actuarial Science and Statistics', name: 'Actuarial Science and Statistics' },
    { id: 'Administration, Secretarial and Personal Assistant', name: 'Administration, Secretarial and Personal Assistant' },
    { id: 'Agriculture, Agronomy and Viticulture', name: 'Agriculture, Agronomy and Viticulture' },
    { id: 'Animal Studies and Veterinary', name: 'Animal Studies and Veterinary' },
    { id: 'Applied Science: Physics and Chemistry', name: 'Applied Science: Physics and Chemistry' },
    { id: 'Archaeology', name: 'Archaeology' },
    { id: 'Architecture and Interior Design', name: 'Architecture and Interior Design' },
    { id: 'Art, Photogrpaphy, Drama, Music and Dance', name: 'Art, Photogrpaphy, Drama, Music and Dance' },
    { id: 'Astronomy and Astrophysics', name: 'Astronomy and Astrophysics' },
    { id: 'Beauty Therapy', name: 'Beauty Therapy' },
    { id: 'Biology', name: 'Biology' },
    { id: 'Biotechnology and Biomedical Science', name: 'Biotechnology and Biomedical Science' },
    { id: 'Business and Management Studies', name: 'Business and Management Studies' },
    { id: 'City, Regional and Town Planning', name: 'City, Regional and Town Planning' },
    { id: 'Clothing and textile science', name: 'Clothing and textile science' },
    { id: 'Computer Science: IT, Programming, informatics, Data Analytics and Systems', name: 'Computer Science: IT, Programming, informatics, Data Analytics and Systems' },
    { id: 'Conservation, Environmental Science and Ecology', name: 'Conservation, Environmental Science and Ecology' },
    { id: 'Education and Social Sciences', name: 'Education and Social Sciences' },
    { id: 'Engineering (Chemical, Civil, Electronic, Mechatronic, Electrical and Industrial)', name: 'Engineering (Chemical, Civil, Electronic, Mechatronic, Electrical and Industrial)' },
    { id: 'Entrepreneurship', name: 'Entrepreneurship' },
    { id: 'Fashion, Film and Video', name: 'Fashion, Film and Video' },
    { id: 'Food Science and Nutrition', name: 'Food Science and Nutrition' },
    { id: 'Forestry and Natural Resource Science', name: 'Forestry and Natural Resource Science' },
    { id: 'General', name: 'General' },
    { id: 'Geology and Geochemistry', name: 'Geology and Geochemistry' },
    { id: 'Health Sciences: Medical, Nursing and Pharmacology', name: 'Health Sciences: Medical, Nursing and Pharmacology' },
    { id: 'Historical Studies', name: 'Historical Studies' },
    { id: 'Human Resource Management', name: 'Human Resource Management' },
    { id: 'Industrial Design and Three-Dimensional Design', name: 'Industrial Design and Three-Dimensional Design' },
    { id: 'Internal Auditing and Auditing', name: 'Internal Auditing and Auditing' },
    { id: 'International Relations', name: 'International Relations' },
    { id: 'Jewellery Design and Manufacture', name: 'Jewellery Design and Manufacture' },
    { id: 'Journalism, Language and Literature', name: 'Journalism, Language and Literature' },
    { id: 'Landscape and Horticulture', name: 'Landscape and Horticulture' },
    { id: 'Law', name: 'Law' },
    { id: 'Logistics, Transport and Supply Chain Management', name: 'Logistics, Transport and Supply Chain Management' },
    { id: 'Marketing, Media, Graphical design and Event Management', name: 'Marketing, Media, Graphical design and Event Management' },
    { id: 'Mathematics and Quantitative Finance', name: 'Mathematics and Quantitative Finance' },
    { id: 'Ocean and Atmosphere Science', name: 'Ocean and Atmosphere Science' },
    { id: 'Office Management and Technology', name: 'Office Management and Technology' },
    { id: 'Optometry, Audiology, Dentistry and Orthodontic', name: 'Optometry, Audiology, Dentistry and Orthodontic' },
    { id: 'Philosophy, Politics and Economics', name: 'Philosophy, Politics and Economics' },
    { id: 'Physiotherapy and Exercise Science', name: 'Physiotherapy and Exercise Science' },
    { id: 'Project Management', name: 'Project Management' },
    { id: 'Property, Construction Studies and Quantity Surverying', name: 'Property, Construction Studies and Quantity Surverying' },
    { id: 'Public Sector', name: 'Public Sector' },
    { id: 'Religious Studies', name: 'Religious Studies' },
    { id: 'Retail Business Management', name: 'Retail Business Management' },
    { id: 'Sports Management', name: 'Sports Management' },
    { id: 'Taxation', name: 'Taxation' },
    { id: 'Travel, Tourism and Hospitality Management', name: 'Travel, Tourism and Hospitality Management' },
    { id: 'Other', name: 'Other' },
  ];

  public citiesWorking = [
      { id: 'Eastern Cape - East London', name: 'Eastern Cape - East London' },
      { id: 'Eastern Cape - Port Elizabeth', name: 'Eastern Cape - Port Elizabeth' },
      { id: 'Eastern Cape - Umtata', name: 'Eastern Cape - Umtata' },
      { id: 'Eastern Cape - Other', name: 'Eastern Cape - Other' },
      { id: 'Free State - Bloemfontein', name: 'Free State - Bloemfontein' },
      { id: 'Free State - Welkom', name: 'Free State - Welkom' },
      { id: 'Free State - Other', name: 'Free State - Other' },
      { id: 'Gauteng - Johannesburg & Sandton', name: 'Gauteng - Johannesburg & Sandton' },
      { id: 'Gauteng - Pretoria', name: 'Gauteng - Pretoria' },
      { id: 'Gauteng - Other', name: 'Gauteng - Other' },
      { id: 'KZN - Durban', name: 'KZN - Durban' },
      { id: 'KZN - Pietermaritzburg', name: 'KZN - Pietermaritzburg' },
      { id: 'KZN - Richards Bay', name: 'KZN - Richard\'s Bay' },
      { id: 'KZN - Other', name: 'KZN - Other' },
      { id: 'Limpopo - Polokwane', name: 'Limpopo - Polokwane' },
      { id: 'Limpopo - Other', name: 'Limpopo - Other' },
      { id: 'Mpumulanga - Mbombela (Nelspruit)', name: 'Mpumulanga - Mbombela (Nelspruit)' },
      { id: 'Mpumulanga - Other', name: 'Mpumulanga - Other' },
      { id: 'North West - Mafikeng', name: 'North West - Mafikeng' },
      { id: 'North West - Potchefstroom', name: 'North West - Potchefstroom' },
      { id: 'North West - Other', name: 'North West - Other' },
      { id: 'Northern Cape - Kimberley', name: 'Northern Cape - Kimberley' },
      { id: 'Northern Cape - Other', name: 'Northern Cape - Other' },
      { id: 'Western Cape - Cape Town', name: 'Western Cape - Cape Town' },
      { id: 'Western Cape - Other', name: 'Western Cape - Other' }
  ];

  public configQualificationLevel = [
    { id: 'NQF 2 - Grade 10', name: 'NQF 2 - Grade 10' },
    { id: 'NQF 4 - Matric', name: 'NQF 4 - Matric' },
    { id: 'NQF 5 - Higher Certificate', name: 'NQF 5 - Higher Certificate' },
    { id: 'NQF 6 - Diploma', name: 'NQF 6 - Diploma' },
    { id: 'NQF 7 - Degree', name: 'NQF 7 - Degree' },
    { id: 'NQF 8 - Honours', name: 'NQF 8 - Honours' },
    { id: 'NQF 9 - Masters', name: 'NQF 9 - Masters' },
    { id: 'NQF 10 - PhD', name: 'NQF 10 - PHD' }
  ];

  public configTertiaryEducation = [
    { id: 'Golden Key', name: 'Golden Key' },
    { id: 'Cum Laude', name: 'Cum Laude' },
    { id: 'Dean’s List', name: 'Dean’s List' }
  ];

  public configYearsWork = [
    { id: '1', name: '0' },
    { id: '2', name: '0 - 1 Year' },
    { id: '3', name: '1 - 2 Year' },
    { id: '4', name: '3 - 5 Years' },
    { id: '5', name: '> 5 Years' }
  ];

  public ethnicityOptions = [
    { id: 'Black', name: 'Black' },
    { id: 'White', name: 'White' },
    { id: 'Coloured', name: 'Coloured' },
    { id: 'Indian', name: 'Indian' },
    { id: 'Oriental', name: 'Oriental' }
  ];

  public ethnicityOptionsYes = [
    { id: 'Black', name: 'Black' },
    { id: 'Coloured', name: 'Coloured' },
    { id: 'Asian', name: 'Asian' },
  ];

  public ethnicityOptionsAll = [
    { id: 'Black', name: 'Black' },
    { id: 'White', name: 'White' },
    { id: 'Coloured', name: 'Coloured' },
    { id: 'Asian', name: 'Asian' },
    { id: 'Foreign National', name: 'Foreign National' },
  ];

  public availabilityOptions = [
    { id: 1, name: 'Immediately' },
    { id: 2, name: 'Within 1 calendar month' },
    { id: 3, name: 'Within 3 calendar months' }
  ];

  public genderOptions = [
    { id: 'Male', name: 'Male' },
    { id: 'Female', name: 'Female' }
  ];

  public firstPopup: boolean;

  public progressBar: number;
  public checkSidebar = false;
  public preloaderView = true;

  public sidebarAdminBadges = new AdminBadges({});
  public sidebarCandidateBadges = new CandidateBadges({});
  public sidebarBusinessBadges = new BusinessBadges({});
  public checkStatusPopup = false;

  constructor(
    protected readonly _http: HttpClient,
    protected readonly _authService: AuthService,
    protected readonly _router: Router,
    private readonly _mapsAPILoader: MapsAPILoader,
    private readonly _ngZone: NgZone,
    private readonly _toastr: ToastrService
  ) {
    super(_http, _authService, _router);
  }

  /**
   * Get Admin badges
   */
  public async getAdminBadges() {
    const headers = await this.createAuthorizationHeader();

    this._http.get<any>('/api/admin/badges', headers)
      .subscribe(data => {
        this.sidebarAdminBadges = new AdminBadges(data);
      })
  }

  /**
   * Reconnect to admin view
   * @param admin {object}
   */
  public goToAdminProfile(admin) {

    localStorage.setItem('access_token', admin.access_token);
    localStorage.setItem('expires_in', admin.expires_in);
    localStorage.setItem('refresh_token', admin.refresh_token);
    localStorage.setItem('role', admin.role);
    localStorage.setItem('id', admin.id);

    localStorage.removeItem('access_token_admin');
    localStorage.removeItem('expires_in_admin');
    localStorage.removeItem('refresh_token_admin');
    localStorage.removeItem('role_admin');
    localStorage.removeItem('id_admin');

    switch (admin.role) {
      case Role.clientRole:
        this._router.navigate(['/business']);
        break;
      case Role.candidateRole:
        this._router.navigate(['/candidate']);
        break;
      case Role.adminRole:
        this._router.navigate(['/admin']);
        break;
      case Role.superAdminRole:
        this._router.navigate(['/admin']);
        break;
      default:
        this._authService.logout();
    }
  }

  /**
   * Get Candidate badges
   */
  public async getCandidateBadges() {
    const headers = await this.createAuthorizationHeader();

    this._http.get<any>('/api/candidate/badges', headers)
      .subscribe(data => {
        this.sidebarCandidateBadges = new CandidateBadges(data);
      })
  }

  /**
   * Get Business badges
   */
  public async getBusinessBadges() {
    const headers = await this.createAuthorizationHeader();

    this._http.get<any>('/api/business/badges', headers)
      .subscribe(data => {
        this.sidebarBusinessBadges = new BusinessBadges(data);
      })
  }

  /**
   * validates all form's fields
   * @param formGroup - group of controls
   * @returns void
   */
  public validateAllFormFields(formGroup: FormGroup): void {
    Object.keys(formGroup.controls).forEach((field) => {
      const control = formGroup.get(field);
      if (control instanceof FormControl) {
        control.markAsTouched({ onlySelf: true });
      } else if (control instanceof FormGroup) {
        this.validateAllFormFields(control);
      } else if (control instanceof FormArray) {
        if (control.controls.length > 0) {
          Object.keys(control.controls).forEach((index) => {
            Object.keys(control.controls[index]['controls']).forEach((groupControl) => {
              control.controls[index]['controls'][groupControl].markAsTouched({ onlySelf: true });
            });
          });
        }
      }
    });
  }

  /**
   * validates all form's fields
   * @param formGroup - group of controls
   * @returns void
   */
  public validateAllFormFieldsJob(formGroup): void {
    Object.keys(formGroup.controls).forEach((field) => {
      const control = formGroup.get(field);
      if (control instanceof FormControl) {
        control.markAsTouched({ onlySelf: true });
      } else if (control instanceof FormGroup) {
        this.validateAllFormFields(control);
      } else if (control instanceof FormArray) {
        if (control.controls.length > 0) {
          Object.keys(control.controls).forEach((index) => {
            Object.keys(control.controls[index]['controls']).forEach((groupControl) => {
              control.controls[index]['controls'][groupControl].markAsTouched({ onlySelf: true });
            });
          });
        }
      }
    });
  }

  /**
   * validates form from display alerts
   * @param form
   */
  public validateAlertCandidateForm(form): void{
    Object.keys(form.controls).forEach((field) => {
      const control = form.get(field);
      if (control instanceof FormControl) {
        if(control.invalid){
          switch (field) {
            case 'firstName':
              this._toastr.error('First Name is required');
              break;
            case 'homeAddress':
              this._toastr.error('Personal Address is required');
              break;
            case 'lastName':
              this._toastr.error('Last Name is required');
              break;
            case 'phone':
              this._toastr.error('Phone is required');
              break;
            case 'email':
              this._toastr.error('Email is required');
              break;
            case 'idNumber':
              this._toastr.error('Please enter a valid South African ID Number.');
              break;
            case 'dateAvailability':
              this._toastr.error('Earliest date of availability is required');
              break;
            case 'citiesWorking':
              this._toastr.error('Cities you would consider working in is required');
              break;
            case 'ethnicity':
              this._toastr.error('Ethnicity is required');
              break;
            case 'nationality':
              this._toastr.error('Nationality is required');
              break;
            case 'gender':
              this._toastr.error('Gender is required');
              break;
            case 'mostRole':
              this._toastr.error('Most Recent Role is required');
              break;
            case 'mostEmployer':
              this._toastr.error('Most Recent Employer is required');
              break;
            case 'englishProficiency':
              this._toastr.error('English Proficiency is required');
              break;
            default:
              break;
          }
        }
      }
    });
  }

  /**
   * validates form from display alerts
   * @param form
   */
  public validateJobForm(form): void{
    Object.keys(form.controls).forEach((field) => {
      const control = form.get(field);
      if (control instanceof FormControl) {
        if(control.invalid){
          switch (field) {
            case 'jobTitle':
              this._toastr.error('Job Title is required');
              break;
            case 'industry':
              this._toastr.error('Industry is required');
              break;
            case 'companyName':
              this._toastr.error('Company Name is required');
              break;
            case 'address':
              this._toastr.error('Address is required');
              break;
            case 'addressCountry':
              this._toastr.error('Address Country is required');
              break;
            case 'addressState':
              this._toastr.error('Address State is required');
              break;
            case 'addressZipCode':
              this._toastr.error('Postal Code is required');
              break;
            case 'addressCity':
              this._toastr.error('Address City is required');
              break;
            case 'addressSuburb':
              this._toastr.error('Address Suburb is required');
              break;
            case 'addressStreet':
              this._toastr.error('Address Street is required');
              break;
            case 'addressStreetNumber':
              this._toastr.error('Address Street Number is required');
              break;
            case 'companyDescription':
              this._toastr.error('Company Description is required');
              break;
            case 'roleDescription':
              this._toastr.error('Role Description is required');
              break;
            case 'closureDate':
              this._toastr.error('Closure Date is required');
              break;
          }
        }
      }
    });
  }

  /**
   * fetches address data from the google API
   * @returns void
   */
  public fetchGoogleAutocompleteDetails(form: FormGroup): void {
    const addressFieldComponent: HTMLElement = document.getElementById('search1');
    this._mapsAPILoader.load().then(() => {
      const autoComplete = new google.maps.places.Autocomplete((<HTMLInputElement>addressFieldComponent), { types: ['address'] });
      autoComplete.addListener('place_changed', () => {
        this._ngZone.run(() => {
          const place: google.maps.places.PlaceResult = autoComplete.getPlace();
          form.controls.address.setValue(place.formatted_address);
          form.controls['addressStreetNumber'].setValue('');
          form.controls['addressStreet'].setValue('');
          form.controls['addressSuburb'].setValue('');
          form.controls['addressCity'].setValue('');
          form.controls['addressState'].setValue('');
          form.controls['addressCountry'].setValue('');
          form.controls['addressZipCode'].setValue('');
          for (let i = 0; i < place.address_components.length; i++) {
            let addressType = place.address_components[i].types[0];
            if (addressType === 'sublocality_level_1') {
              addressType = 'sublocality_level_2';
            }
            if (this.componentForm[addressType]) {
              const valuePlace = place.address_components[i][this.componentForm[addressType]];
              (<HTMLInputElement>document.getElementById(addressType)).value = valuePlace;

              if (addressType === 'street_number') {
                form.controls['addressStreetNumber'].setValue(valuePlace);
              } else if (addressType === 'sublocality_level_2') {
                form.controls['addressSuburb'].setValue(valuePlace);
              } else if (addressType === 'route') {
                form.controls['addressStreet'].setValue(valuePlace);
              } else if (addressType === 'locality') {
                form.controls['addressCity'].setValue(valuePlace);
              } else if (addressType === 'administrative_area_level_1') {
                form.controls['addressState'].setValue(valuePlace);
              } else if (addressType === 'country') {
                form.controls['addressCountry'].setValue(valuePlace);
              } else if (addressType === 'postal_code') {
                form.controls['addressZipCode'].setValue(valuePlace);
              }
            }
          }
          if ( place.geometry === undefined || place.geometry === null ) {
            return;
          }
        });
      });
    });
  }

  /**
   * shows all errors performing requests
   * @param error
   */
  public showRequestErrors(error: any) {
    if (error.status === 403){
      window.location.reload();
    }
    else if (error.status === 401){
      this._authService.logout();
      window.location.reload();
    }
    else{
      if (error.error) {
        if (typeof error.error.error === 'string') {
          this._toastr.error(error.error.error);
        } else {
          error.error.error.forEach(data => {
            this._toastr.error(data);
          });
        }
      }
    }
  }

  /**
   * reset from
   * @param form
   * @returns void
   */
  public resetForm(form: FormGroup): void {
    form.reset();
  }

  /**
   * gets availability of business job in human readable form
   * @param availability {number} - integer representation of job availability {0, 1, 2 or 3}
   * @returns {string}
   */
  public getAvailabilityInHumanReadableForm(availability: number): string {
    const availabilityPossibleValues = {
      0: 'All',
      1: 'Immediately ',
      2: 'Within 1 calendar month ',
      3: 'Within 3 calendar months ',
    };
    return availabilityPossibleValues[availability];
  }

  /**
   * gets nationality in human readable form
   * @param nationality {number} - integer representation of nationality {0, 1 or 2}
   * @returns {string}
   */
  public getNationalityInHumanReadableForm(nationality: number): string {
    const nationalityPossibleValues = {
      0: 'All',
      1: 'South African Citizen (BBBEE)',
      2: 'South African Citizen (Non-BBBEE)',
      3: 'Non-South African (With Permit)',
      4: 'Non-South African (Without Permit)'
    };
    return nationalityPossibleValues[nationality];
  }

  /**
   * gets qualification in human readable form
   * @param qualification {number} - integer representation of qualification {0, 1 or 2}
   * @returns {string}
   */
  public getQualificationInHumanReadableForm(qualification: number): string {
    const qualificationPossibleValues = {
      0: 'Newly qualified CA',
      1: 'Newly qualified CA',
      2: 'Part qualified CA',
    };
    return qualificationPossibleValues[qualification];
  }

  /**
   * gets boards in human readable form
   * @param boards - integer representation of qualification {1, 2, 3 or 4}
   * @returns {any}
   */
  public getBoardsInHumanReadableForm(boards: number): string {
    const boardsPossibleValues = {
      1: 'Passed Both Board Exams First Time',
      2: 'Passed Both Board Exams',
      3: 'ITC passed, APC Outstanding',
      4: 'ITC Outstanding',
    };
    return boardsPossibleValues[boards];
  }

  /**
   * gets availability in human readable form
   * @param availability {number} - integer representation of availability true - immediate, false - date of availability
   * @param availabilityPeriod {number}
   * @param dateAvailability {string} - data when candidate is available
   * @returns {any}
   */
  public getCandidateAvailabilityInHumanReadableForm(availability: boolean, availabilityPeriod: number, dateAvailability: string): string {
    const thingToShow = (new Date(dateAvailability).getTime() <  new Date().getTime())
        ? 'Immediate'
        : new Date(dateAvailability).toDateString();
    let returnDate;
    if (availability) {
      returnDate = 'Immediate';
    }
    else if(availabilityPeriod === null) {
      returnDate = '-';
    }
    else if(availabilityPeriod !== 4){
      if(availabilityPeriod === 1){
        returnDate = '30 Day notice period';
      }
      else if(availabilityPeriod === 2){
        returnDate = '60 Day notice period';
      }
      else if(availabilityPeriod === 3){
        returnDate = '90 Day notice period';
      }
    }
    else if(dateAvailability === null){
      returnDate = '-';
    }
    else{
      returnDate = thingToShow;
    }
    return returnDate;
  }

  /**
   * gets difference in days between two dates
   * @param date1
   * @param date2
   * @returns {number}
   */
  public getDifferenceInDays(date1, date2): number {
    const endDate = new Date(date2);
    const startDate = new Date(date1);
    const diffDays = Math.floor((endDate.getTime() - startDate.getTime()) / (1000 * 60 * 60 * 24));
    return diffDays;
  }
}
